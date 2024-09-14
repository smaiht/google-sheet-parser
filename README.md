# Google Sheet Parser with Yii2 and MongoDB

### Overview
https://github.com/user-attachments/assets/f6ddcac8-a6db-4b79-99a6-56c3ce879221
Showcases integration of Google Sheets API, Yii2 framework, CLI, and MongoDB for data fetching, management, and analysis in a Docker container.
![Project Demo](https://github.com/user-attachments/assets/f6ddcac8-a6db-4b79-99a6-56c3ce879221)

### Key Features
- **Google Sheet Integration**: Parses and fetches data from specified table
- **MongoDB Storage**: Stores parsed data for quick retrieval and analysis
- **Dynamic Frontend Display**: Interactive table format
- **Change Tracking**: 
  - Highlights edited cells
  - Maintains logs of data changes
- **Version Control**: Access to historical table snapshots
- **CLI Functionality**: Console command for automated data fetching
- **Query Interface**:
  - Selection of multiple products, options, and months
  - Generates customized data combinations


### Installation

INSTALLATION
------------

# 1. Using Docker

Clone this repository and set up the environment:
~~~
git clone https://github.com/smaiht/google-sheet-parser.git
~~~
Edit `.env` file with your `MONGODB_STRING` connection string and `SERVICE_ACC_JSON_PATH` Google service account details:
- Set MONGODB_STRING to your MongoDB connection URL
- Set SERVICE_ACC_JSON_PATH to the path of your Google service account JSON file

Note: You need to create a Google service account, enable Google Sheets API access,
create a new key, and save it as a JSON file in the `/config` folder.
`Alternatively, contact me for a sample JSON file.`

Build and run the Docker containers:
~~~
cd google-sheet-parser/
docker-compose up --build
~~~
Open [http://localhost:8337/](http://localhost:8337/)


# 2. Using local enviroment

Clone the repository and install dependencies:
~~~
git clone https://github.com/smaiht/google-sheet-parser.git
~~~
Edit `.env` file with your `MONGODB_STRING` connection string and `SERVICE_ACC_JSON_PATH` Google service account details:
- Set MONGODB_STRING to your MongoDB connection URL
- Set SERVICE_ACC_JSON_PATH to the path of your Google service account JSON file

Note: You need to create a Google service account, enable Google Sheets API access,
create a new key, and save it as a JSON file in the `/config` folder.
`Alternatively, contact me for a sample JSON file.`
~~~
cd google-sheet-parser/
composer install
~~~
Start the local development server:
~~~
php yii serve
~~~
Access the application:
Open the URL provided by Yii2 (typically [http://localhost:8080/](http://localhost:8080/))

Note: Ensure you have PHP and Composer installed on your local machine for the second method.
