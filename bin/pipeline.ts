import * as cdk from 'aws-cdk-lib';
import { AppStack, AppStackProps } from '../lib/app-stack';
import { SetupStack } from '../lib/setup-stack';

const prdAppStackProps: AppStackProps = {
    env: {
        account: process.env.CDK_DEFAULT_ACCOUNT,
        region: "us-east-1"
    },
    hostedZoneDomainName: "desire2learnvalence.com",
    certificateDomain: "apitesttool.desire2learnvalence.com",
    fargateDomainName: "apitesttool.desire2learnvalence.com",
    elbLogTargetEnvironment: "prd",
    imageTag: process.env.IMAGE_TAG
}

const devAppStackProps: AppStackProps = {
    env: {
        account: "111111111111",
        region: "ca-central-1",
    },
    hostedZoneDomainName: "lti.dev.brightspace.com",
    certificateDomain: "apitesttool.lti.dev.brightspace.com",
    fargateDomainName: "apitesttool.lti.dev.brightspace.com",
    elbLogTargetEnvironment: "dev",
    imageTag: "test1"
}

const app = new cdk.App();

new SetupStack(app, "ApiTestToolSetupStack", prdAppStackProps );
new AppStack(app, "ApiTestToolAppStack", prdAppStackProps );

new SetupStack(app, "ApiTestToolSetupStack-Dev", devAppStackProps );
new AppStack(app, "ApiTestToolAppStack-Dev", devAppStackProps );

app.synth();
