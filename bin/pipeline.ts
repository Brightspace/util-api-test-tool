import * as cdk from '@aws-cdk/core';
import { AppStack, AppStackProps } from '../lib/app-stack';
import { SetupStack } from '../lib/setup-stack';

const prdAppStackProps: AppStackProps = {
    env: {
        account: process.env.CDK_DEFAULT_ACCOUNT,
        region: "us-east-1",
    },
    hostedZoneDomainName: "desire2learnvalence.com",
    certificateDomain: "apitesttool.desire2learnvalence.com",
    fargateDomainName: "apitesttool.desire2learnvalence.com",
    elbLogS3BucketName: "api-test-tool-access-logs2",
    imageTag: process.env.IMAGE_TAG
}

const devAppStackProps: AppStackProps = {
    env: {
        account: "",
        region: "us-east-2",
    },
    hostedZoneDomainName: "lti.dev.brightspace.com",
    certificateDomain: "apitesttool.lti.dev.brightspace.com",
    fargateDomainName: "apitesttool.lti.dev.brightspace.com",
    elbLogS3BucketName: "api-test-tool-access-logs-dev6",
    imageTag: "test1"
}

const app = new cdk.App();

new SetupStack(app, "ApiTestToolSetupStack", prdAppStackProps as cdk.StackProps );
new AppStack(app, "ApiTestToolAppStack", prdAppStackProps );

new SetupStack(app, "ApiTestToolSetupStack-Dev", devAppStackProps as cdk.StackProps );
new AppStack(app, "ApiTestToolAppStack-Dev", devAppStackProps );

app.synth();
