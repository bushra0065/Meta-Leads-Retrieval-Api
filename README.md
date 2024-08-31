**Meta Leads Retrieval API**
This repository contains a PHP-based API designed to retrieve leads from Meta (formerly Facebook) Ads. The API is built to streamline the process of gathering lead data and integrating it directly into your internal systems, such as a sales department portal. This helps save time and resources by automating the lead retrieval process.

**Features**
Lead Retrieval: Automatically fetches leads generated from Meta Ads.
Token Management: Utilizes a long-lived access token to authenticate requests. The token is refreshed automatically using a cron job, ensuring continuous and secure access to the Meta Ads API.
Data Integration: Directly assigns leads to relevant sales departments or CRM systems, improving workflow efficiency.
**Installation**
Clone the repository:

bash
Copy code
git clone https://github.com/bushra0065/Meta-Leads-Retrieval-Api.git
cd Meta-Leads-Retrieval-Api
Install required dependencies (if any).

Configure your environment variables and API credentials within the project files to match your Meta Ads account settings.

**Usage**
Retrieve Token: Use the getToken.php script to generate and retrieve the access token necessary for API requests.

bash
Copy code
php getToken.php
**Fetch Leads:** Use the API to retrieve leads and process them according to your business logic.

**Cron Job:** Set up a cron job using the provided forserverrefresh_token.bat script to automatically refresh the token and maintain uninterrupted access.

**Contributing**
Contributions are welcome! Please fork this repository, create a feature branch, and submit a pull request for review.
