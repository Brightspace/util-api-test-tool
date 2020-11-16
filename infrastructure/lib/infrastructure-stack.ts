import * as cdk from '@aws-cdk/core';
import * as ec2 from "@aws-cdk/aws-ec2";
import * as ecs from "@aws-cdk/aws-ecs";
import * as ecs_patterns from "@aws-cdk/aws-ecs-patterns";
import image_asset = require('@aws-cdk/aws-ecr-assets');
import * as path from 'path';



export class InfrastructureStack extends cdk.Stack {

  constructor(scope: cdk.Construct, id: string, props?: cdk.StackProps) {
    super(scope, id, props);

    const vpc = new ec2.Vpc(this, "MyVpc", {
      maxAzs: 2 // Default is all AZs in region
    });

    const cluster = new ecs.Cluster(this, "MyCluster", {
      vpc: vpc
    });

    const asset = new image_asset.DockerImageAsset(this, 'ApiTestToolImage', {
      directory: path.join(__dirname, '/../../src/')
    });

    //asset.repository.grantPull(principal)
    // Create a load-balanced Fargate service and make it public
    new ecs_patterns.ApplicationLoadBalancedFargateService(this, "MyFargateService", {
      cluster: cluster, // Required
      cpu: 512, // Default is 256
      desiredCount: 1, // Default is 1
      taskImageOptions: { image: ecs.ContainerImage.fromDockerImageAsset(asset)},//.fromRegistry("amazon/amazon-ecs-sample") },
      memoryLimitMiB: 2048, // Default is 512
      publicLoadBalancer: true // Default is false
    });
  }
}

const app = new cdk.App();
new InfrastructureStack(app, 'ApiTestToolInfrastructureStack');
app.synth();