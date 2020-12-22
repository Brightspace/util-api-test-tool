import 'source-map-support/register';
import * as cdk from '@aws-cdk/core';
import { AppStack } from '../lib/app-stack';
import { SetupStack } from '../lib/setup-stack'

const app = new cdk.App();
const region = "us-west-1";
const account = '084374970894';

new SetupStack(app, "ApiTestToolSetupStack", {
    env: { 
        account: account, 
        region: region 
    }
});
  
const appStack = new AppStack(app, "ApiTestToolAppStack", {
    env: { 
        account: account, 
        region: region
    }
});

console.log("LoadBalancer" + appStack.urlOutput);

app.synth();