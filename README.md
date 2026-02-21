# LM

## Deployment to AWS Elastic Beanstalk

This repository can be deployed automatically from GitHub using a GitHub Actions pipeline that builds a zip and publishes it to AWS Elastic Beanstalk.

Summary steps:
- Create an Elastic Beanstalk Application and Environment (platform: PHP / Amazon Linux 2)
- Create an S3 bucket for application versions
- Create an IAM user with programmatic access and permissions to EB and S3
- Add the required GitHub repository secrets (see below)
- Push to `main` to trigger the workflow

Required GitHub secrets (Repository Settings → Secrets → Actions):
- `AWS_ACCESS_KEY_ID` — IAM user's access key id
- `AWS_SECRET_ACCESS_KEY` — IAM user's secret access key
- `AWS_REGION` — e.g. `us-east-1`
- `EB_APPLICATION_NAME` — your Elastic Beanstalk application name
- `EB_ENVIRONMENT_NAME` — your Elastic Beanstalk environment name
- `S3_BUCKET` — name of the S3 bucket to hold application versions

Minimal IAM policy (attach to a GitHub Actions user) — adapt least privilege for your org:

```json
{
	"Version": "2012-10-17",
	"Statement": [
		{
			"Effect": "Allow",
			"Action": [
				"elasticbeanstalk:CreateApplicationVersion",
				"elasticbeanstalk:UpdateEnvironment",
				"elasticbeanstalk:DescribeApplications",
				"elasticbeanstalk:DescribeEnvironments"
			],
			"Resource": "*"
		},
		{
			"Effect": "Allow",
			"Action": [
				"s3:PutObject",
				"s3:GetObject",
				"s3:ListBucket"
			],
			"Resource": [
				"arn:aws:s3:::YOUR_BUCKET_NAME",
				"arn:aws:s3:::YOUR_BUCKET_NAME/*"
			]
		}
	]
}
```

Notes:
- Ensure your PHP runtime on Elastic Beanstalk matches your code (PHP 8.1 recommended).
- Exclude secrets and local-only files from the deploy zip (the workflow excludes `.env`, `vendor/`, etc.).
- If your app depends on a database, create an RDS instance (or use external DB) and configure environment variables in EB.

Trigger: pushing to `main` will run the workflow and deploy the current commit as a new EB application version.
