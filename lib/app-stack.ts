import * as cdk from 'aws-cdk-lib';
import { aws_s3 as s3, aws_ec2 as ec2, aws_ecs as ecs } from 'aws-cdk-lib';
import { aws_ecs_patterns as ecs_patterns, aws_ecr as ecr } from 'aws-cdk-lib';
import { aws_certificatemanager as cert, aws_route53 as route53 } from 'aws-cdk-lib';
import { SslPolicy } from 'aws-cdk-lib/aws-elasticloadbalancingv2';

export interface AppStackProps extends cdk.StackProps {
  /**
   * hosted zone domain name
   * "desire2learnvalence.com"
   */
  readonly hostedZoneDomainName: string;
  /**
   * certificate domain name
   * "apitesttool.desire2learnvalence.com"
   */
  readonly certificateDomain: string;

  /**
   * fargateDomainName
   * @Default: "apitesttool.desire2learnvalence.com"
   */
  readonly fargateDomainName: string;

  /**
   * imageTag
   */
  readonly imageTag?: string;

  /**
   * s3 elb bucket logs
   * @Default: "prd or dev"
   */
  readonly elbLogTargetEnvironment: string;
}

export class AppStack extends cdk.Stack {

  constructor(scope: cdk.App, id: string, props: AppStackProps) {
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

    const hostedZone = route53.HostedZone.fromLookup( this, "HostedZone", {
      domainName: props.hostedZoneDomainName
    });

    const certificate = new cert.Certificate(this, 'Certificate', {
      domainName: props.certificateDomain,
      validation: cert.CertificateValidation.fromDns(hostedZone),
    });

    // Create a load-balanced Fargate service and make it public
    const fargateService = new ecs_patterns.ApplicationLoadBalancedFargateService(this, "ApiTestToolFargateService", {
      cluster: cluster, // Required
      cpu: 256, // Default is 256
      desiredCount: 1, // Default is 1
      taskImageOptions: {
        image: ecs.ContainerImage.fromEcrRepository(
            repository,
            props.imageTag
      )},
      memoryLimitMiB: 2048, // Default is 512
      publicLoadBalancer: true, // Default is false,
      redirectHTTP: true,
      domainName: props.fargateDomainName,
      domainZone: hostedZone,
      certificate: certificate,
      sslPolicy: SslPolicy.FORWARD_SECRECY_TLS12
    });

    const scaling = fargateService.service.autoScaleTaskCount({
      maxCapacity: 3,
      minCapacity: 1
    });

    scaling.scaleOnCpuUtilization('CpuScaling', {
      targetUtilizationPercent: 50,
      scaleInCooldown: cdk.Duration.seconds(60),
      scaleOutCooldown: cdk.Duration.seconds(60)
    });

    const region = props?.env?.region;
    const lb_log_bucket = `d2l-alb-logs-ingestion-${props.elbLogTargetEnvironment}-${region}`;

    const logBucket = s3.Bucket.fromBucketName(this, 'ALBLogBuck', lb_log_bucket);

    fargateService.loadBalancer.logAccessLogs( logBucket );

    fargateService.loadBalancer.setAttribute( "routing.http.drop_invalid_header_fields.enabled", "true" );

    const urlOutput = new cdk.CfnOutput(this, 'LoadBalancerDNS', { value: fargateService.loadBalancer.loadBalancerDnsName });
    console.log(urlOutput);

  }
}
