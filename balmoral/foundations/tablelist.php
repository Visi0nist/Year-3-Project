<?php

// tablelist.php
// 20220707 2042
// KMB

// BALMORAL 

// a user management suite

// ********************************************************************************			
   
// This file serves two purposes

// 1. it is a permanent record of all tables
// 2. it assembles the array invokeTableList which enables invoke.php to create tables

// every entity on the system has a "systemno"
// a person, a company, a book, an asset, etc
// the variable "mSystemno" is the one that links everything together

// only "a person" can be a "user", and
// mUserLoginText is their unique email address (in theory it can be some other unique value)

// ********************************************************************************			
// variables

$invokeTableList = array();

// ********************************************************************************			
// DB       balmoral			
// table    error_code			
// 20230320 0929	

$query = "			
			
CREATE TABLE error_code (			
	mErrorCode	            INT(7)	NOT NULL UNIQUE,
	mErrorText               VARCHAR(30),	
			
	PRIMARY KEY (mErrorCode),
	INDEX (mErrorText)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;						
";	
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    system_button_bond			
// 20221218 1328
			
$query = "			
			
CREATE TABLE system_button_bond (			
	mButtonBondno	        BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mSystemno               BIGINT(11)	NOT NULL,
	mButtonDatano	        BIGINT(7)	NOT NULL,
	mButtonStartDateTime	DATETIME,	
	mButtonEndDateTime	    DATETIME,	
			
	PRIMARY KEY     (mButtonBondno),		
	INDEX           (mSystemno),		
	INDEX           (mButtonDatano),
	INDEX           (mButtonStartDateTime),		
	INDEX           (mButtonEndDateTime)
)			
			
DEFAULT CHARACTER SET utf8			
COLLATE utf8_general_mysql500_ci;			
			
";	


$invokeTableList[] = $query;

// ********************************************************************************			
// DB       balmoral			
// table    button_text			
// 20221218 1328
			
$query = "			
			
CREATE TABLE button_text (			
	mButtonDatano	BIGINT(7)	NOT NULL AUTO_INCREMENT,
	mButtonText	    VARCHAR(30)	NOT NULL,
			
	PRIMARY KEY     (mButtonDatano),		
	INDEX           (mButtonText)		
)			
			
DEFAULT CHARACTER SET utf8			
COLLATE utf8_general_mysql500_ci;			
			
";

$invokeTableList[] = $query;


// ********************************************************************************			
// DB       balmoral			
// table    system_token_bond			
// 20221211 1521			
			
$query = "			
			
CREATE TABLE system_token_bond (			
	mSystemTokenBondno	BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mSystemno	        BIGINT(11),	
	mTokenDatano	    BIGINT(11),	
	mTokenStartDate	    DATETIME,	
			
	PRIMARY KEY (mSystemTokenBondno),		
	INDEX (mSystemno),		
	INDEX (mTokenDatano)
)			
			
DEFAULT CHARACTER SET utf8			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    token		
// 20221211 1248			
			
$query = "			
			
CREATE TABLE token (			
	mTokenDatano	BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mTokenLogin	    VARCHAR(20),
	mSession	    VARCHAR(36),	
			
	PRIMARY KEY (mTokenDatano),		
	INDEX (mTokenLogin),		
	INDEX (mSession)
)			
			
DEFAULT CHARACTER SET utf8			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			


// ********************************************************************************			
// DB       balmoral			
// table    system_entity_bond			
// 20220812 1428			
			
$query = "			
			
CREATE TABLE system_entity_bond (			
	mEntityBondno	BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mSystemno	    BIGINT(11),	
	mEntityno	    INT(7),	
			
	PRIMARY KEY (mEntityBondno),		
	INDEX (mSystemno),		
	INDEX (mEntityno)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    entity_text			
// 20220812 1427			
			
$query = "			
			
CREATE TABLE entity_text (			
	mEntityno	INT(7)	NOT NULL AUTO_INCREMENT,
	mEntityText	VARCHAR(50),	
			
	PRIMARY KEY (mEntityno),		
	INDEX (mEntityText)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    weeding_days			
// 20220806 1843	

// mWeedingDesc - the purpose - eg, search results, token
// mWeedingText - short text for time limit - eg, 7 days, 12 months 
		
			
$query = "			
			
CREATE TABLE weeding_days (			
	mWeedingDatano	TINYINT(2)	NOT NULL AUTO_INCREMENT,
	mWeedingDesc	VARCHAR(50) UNIQUE,	
	mWeedingDays	DECIMAL(10,4),
	mWeedingText	VARCHAR(50) UNIQUE,	
			
	PRIMARY KEY (mWeedingDatano),		
	INDEX (mWeedingDesc),
	INDEX (mWeedingText)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    maintenance			
// 20220806 1836			
			
$query = "			
			
CREATE TABLE maintenance (			
	mMaintenanceDatano	TINYINT(2)	NOT NULL AUTO_INCREMENT,
	mMaintenanceFlagUMP	TINYINT(2),	
			
	PRIMARY KEY (mMaintenanceDatano),		
	INDEX (mMaintenanceFlagUMP)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			


// ********************************************************************************			
// DB       balmoral			
// table    search_results			
// 20230320 1140		
			
$query = "			
			
CREATE TABLE search_results (			
	mResultno	    BIGINT(11)      NOT NULL AUTO_INCREMENT,
	mSearchno	    BIGINT(11),	
	mSearchCriteria	VARCHAR(100),
	mSearchDate	    DATETIME,	
	mSystemno	    BIGINT(11),	
	mAction	        VARCHAR(20),
	mResultText	    VARCHAR(200),
			
	PRIMARY KEY (mResultno),		
	INDEX (mSearchno)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    user_status_bond			
// 20220712 0800			
			
$query = "			
			
CREATE TABLE system_status_bond (			
	mSystemStatusBondno	    BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mSystemno	            BIGINT(11),	
	mStatusDatano        	INT(7),	
	mStatusStartDateTime	DATETIME,	
	mStatusEndDateTime	    DATETIME,	
			
	PRIMARY KEY (mSystemStatusBondno),		
	INDEX (mSystemno),		
	INDEX (mStatusDatano),		
	INDEX (mStatusStartDateTime),		
	INDEX (mStatusEndDateTime)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    status_text			
// 20220712 0749			
			
$query = "			
			
CREATE TABLE status_text (			
	mStatusDatano	INT(7)	NOT NULL AUTO_INCREMENT,
	mStatusText	    VARCHAR(50),
			
	PRIMARY KEY (mStatusDatano),		
	INDEX (mStatusText)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			


// ********************************************************************************			
// DB       balmoral			
// table    activity_code			
// 20220712 0725		

$query = "			
			
CREATE TABLE activity_code (			
	mActivityCode	            INT(7)	NOT NULL UNIQUE,
	mActivityText               VARCHAR(30),	
			
	PRIMARY KEY (mActivityCode),
	INDEX (mActivityText)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;						
";	
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    user_grade_bond			
// 20220711 0848			
			
$query = "			
			
CREATE TABLE system_grade_bond (			
	mGradeBondno	    BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mSystemno           BIGINT(11),	
	mGradeDatano  	    INT(7),	
	mGradeStartDateTime	DATETIME,	
	mGradeEndDateTime	DATETIME,	
			
	PRIMARY KEY (mGradeBondno),		
	INDEX (mGradeDatano)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
			
";			

$invokeTableList[] = $query;	


// ********************************************************************************			
// DB       balmoral			
// table    grade_text
// 20220711 0848			
			
$query = "			
			
CREATE TABLE grade_text (			
	mGradeDatano	INT(7)	    NOT NULL AUTO_INCREMENT,
	mGradeText	    VARCHAR(40),	
			
	PRIMARY KEY (mGradeDatano)		
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
			
";			

$invokeTableList[] = $query;	

// ********************************************************************************			
// DB       balmoral			
// table    system_name_bond			
// 20220711 0844			
			
$query = "			
			
CREATE TABLE system_name_bond (			
	mSytemNameBondno  	BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mSystemno	        BIGINT(11),	
	mNameDatano	        BIGINT(11)  NOT NULL,
	mNameStartDate	    DATETIME,	
	mNameEndDate	    DATETIME,	
			
	PRIMARY KEY (mSytemNameBondno),		
	INDEX (mNameDatano)
	
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
			
";	
			
$invokeTableList[] = $query;	

// ********************************************************************************			
// DB       balmoral			
// table    name_text			
// 20220711 0844			
			
$query = "			
			
CREATE TABLE name_text (			
	mNameDatano	          BIGINT(11)    NOT NULL AUTO_INCREMENT,
	mNameSurname	      VARCHAR(200)	NOT NULL,
	mNameFirstName	      VARCHAR(200)	NOT NULL,
	mNameMiddleName	      VARCHAR(200),
	mNamePronunciation	  VARCHAR(200),
	mNameKnownAs     	  VARCHAR(200),
			
	PRIMARY KEY (mNameDatano),		
	INDEX (mNameSurname),		
	INDEX (mNameFirstName),		
	INDEX (mNameKnownAs)		
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
			
";		
			
$invokeTableList[] = $query;	

// ********************************************************************************			
// DB       balmoral			
// table    system			
// 20220707 2105			
			
// mUserLoginText can be null, so that SMT can add new user by mSystemno and mNameSurname if needed

$query = "			
			
CREATE TABLE system (			
	mSystemno	        	    BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mUserLoginText	    	    VARCHAR(80),
	mUserPassword	    	    VARCHAR(80),	
	mLocalRefno	        	    VARCHAR(50),	
	mSystemStartDateTime	    DATETIME,	
	mSystemSoftDeleteDateTime   DATETIME,	
	mSystemEndDateTime	        DATETIME,
			
	PRIMARY KEY (mSystemno),		
	INDEX (mUserLoginText)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    log			
// 20220707 2053			

// log is both an activity log and a task log
			
$query = "			
			
CREATE TABLE log (			
	mLogno	        BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mSystemno	    BIGINT(11),	
	mActivityCode   INT(7),		
	mLogDateTime    DATETIME,	
	mOperand	    BIGINT(11),	
	mScript	        VARCHAR(50),
	mWebapp	        VARCHAR(50),
	mErrCode	    INT(7),	
	mNarrative	    VARCHAR(300),	
	mIPAddress	    VARCHAR(40),	
	mHTTPReferrer	VARCHAR(200),
			
	PRIMARY KEY (mLogno),		
	INDEX (mSystemno),		
	INDEX (mLogDateTime),		
	INDEX (mOperand)
)			
			
DEFAULT CHARACTER SET utf8 			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

?>