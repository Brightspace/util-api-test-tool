import * as cdk from '@aws-cdk/core';
import { AppStack } from '../lib/app-stack';
import { SetupStack } from '../lib/setup-stack'

const app = new cdk.App();

new SetupStack(app, "ApiTestToolSetupStack", {
    env: { 
        account: process.env.CDK_DEFAULT_ACCOUNT, 
        region: "us-east-1"
    }
});
  
const appStack = new AppStack(app, "ApiTestToolAppStack", {
    env: { 
        account: process.env.CDK_DEFAULT_ACCOUNT, 
        region: "us-east-1"
    }
});

console.log("LoadBalancer" + appStack.urlOutput);

app.synth();
