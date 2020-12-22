import * as cdk from '@aws-cdk/core';
import * as ec2 from "@aws-cdk/aws-ec2";
import * as ecs from "@aws-cdk/aws-ecs";
import * as ecs_patterns from "@aws-cdk/aws-ecs-patterns";
import image_asset = require('@aws-cdk/aws-ecr-assets');
import * as path from 'path';
import * as ecr from "@aws-cdk/aws-ecr";
import { LifecyclePolicy } from '@aws-cdk/aws-efs';

export class SetupStack extends cdk.Stack {

  constructor(scope: cdk.Construct, id: string, props?: cdk.StackProps) {
    super(scope, id, props);

    // ECR repository
    const repository = new ecr.Repository(this, "api_test_tool_repository", {
      repositoryName: "api_test_tool_repository",
      imageScanOnPush: true,
      removalPolicy: cdk.RemovalPolicy.RETAIN,
      //lifecycleRules : [LifecycleRule]
    });

  }
}
/*
const app = new cdk.App();
new SetupStack(app, 'SetupStack', {
  env: { 
    account: '865619583185', 
    region: 'us-east-2' 
}});
app.synth();

*/