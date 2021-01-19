import * as cdk from '@aws-cdk/core';
import * as ec2 from "@aws-cdk/aws-ec2";
import * as ecs from "@aws-cdk/aws-ecs";
import * as s3 from "@aws-cdk/aws-s3";
import * as ecs_patterns from "@aws-cdk/aws-ecs-patterns";
import * as ecr from "@aws-cdk/aws-ecr";
import * as cert from '@aws-cdk/aws-certificatemanager';
import * as route53 from '@aws-cdk/aws-route53';

export class AppStack extends cdk.Stack {

  public readonly urlOutput: cdk.CfnOutput;

  constructor(scope: cdk.Construct, id: string, props?: cdk.StackProps) {
    super(scope, id, props);

    // ECR repository
    const repository = ecr.Repository.fromRepositoryName(this, 
      "RepositoryId", 
      "api_test_tool_repository"
    );

    const vpc = new ec2.Vpc(this, "ApiTestToolVpc", {
      maxAzs: 2// Default is all AZs in region
    });

    const cluster = new ecs.Cluster(this, "ApiTestToolCluster", {
      vpc: vpc
    });

    /*const hostedZone = route53.HostedZone.fromLookup( this, "HostedZone", {
      domainName : "desire2learnvalence.com"
    });

    const certificate = new cert.Certificate(this, 'Certificate', {
      domainName : "apitesttool2.desire2learnvalence.com",
      validation: cert.CertificateValidation.fromDns(hostedZone),
    });*/

    // Create a load-balanced Fargate service and make it public
    const fargateService = new ecs_patterns.ApplicationLoadBalancedFargateService(this, "ApiTestToolFargateService", {
      cluster: cluster, // Required
      cpu: 512, // Default is 256
      desiredCount: 2, // Default is 1
      taskImageOptions: { 
        image: ecs.ContainerImage.fromEcrRepository(
            repository, 
            process.env.IMAGE_TAG
      )},
      memoryLimitMiB: 2048, // Default is 512
      publicLoadBalancer: true, // Default is false,
      redirectHTTP: false, 
      //domainName: "apitesttool2.desire2learnvalence.com",	//string	The domain name for the service, e.g. "api.example.com.".
      //domainZone: hostedZone,
      //certificate: certificate
    });

    const scaling = fargateService.service.autoScaleTaskCount({ 
      maxCapacity: 3,
      minCapacity: 2 
    });

    scaling.scaleOnCpuUtilization('CpuScaling', {
      targetUtilizationPercent: 50,
      scaleInCooldown: cdk.Duration.seconds(60),
      scaleOutCooldown: cdk.Duration.seconds(60)
    });

    const logBucket = new s3.Bucket(this, 'S3AccessLogs', {
      bucketName : "api-test-tool-access-logs2",
      lifecycleRules: [{
        expiration: cdk.Duration.days(365),
        transitions: [{
            storageClass: s3.StorageClass.INFREQUENT_ACCESS,
            transitionAfter: cdk.Duration.days(30)
        },{
            storageClass: s3.StorageClass.GLACIER,
            transitionAfter: cdk.Duration.days(90)
        }]
      }]
    });

    fargateService.loadBalancer.logAccessLogs( logBucket );

    this.urlOutput = new cdk.CfnOutput(this, 'LoadBalancerDNS', { value: fargateService.loadBalancer.loadBalancerDnsName });

  }
}
