import * as cdk from '@aws-cdk/core';
import * as ecr from "@aws-cdk/aws-ecr";

export class SetupStack extends cdk.Stack {

  constructor(scope: cdk.Construct, id: string, props?: cdk.StackProps) {
    super(scope, id, props);

    // ECR repository
    const repository = new ecr.Repository(this, "api_test_tool_repository", {
      repositoryName: "api_test_tool_repository",
      imageScanOnPush: true,
      removalPolicy: cdk.RemovalPolicy.RETAIN,
    });
  }
}