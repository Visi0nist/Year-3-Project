<?php

// tablelist.php
// 20230321 0934
// KMB

// WINDSOR
// an add in for BALMORAL 

// Long Term Tracker
// This is a permanent record of all tables for use in only the Long Term Tracker
// the tables are added to the existing Balmoral database

// ********************************************************************************			
// DB       balmoral			
// table    consumer_button_bond			
// 20221218 1328
			
$query = "			
			
CREATE TABLE consumer_button_bond (			
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
// table    weight			
// 20230321 1012			
			
$query = "			
			
CREATE TABLE weight (			
	mWeightno	BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mSystemno	BIGINT(11),	
	mWeightDate	DATETIME,	
	mKilograms	DECIMAL(8,4),
			
	PRIMARY KEY (mWeightno),
	INDEX (mSystemno)
)			
			
DEFAULT CHARACTER SET utf8			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    performance			
// 20230321 1010			
			
$query = "			
			
CREATE TABLE performance (			
	mPerformanceno	    BIGINT(11)	NOT NULL AUTO_INCREMENT,
	mSystemno	        BIGINT(11),	
	mPursuitno	        INT(7),	
	mPursuitDate	    DATETIME,	
	mPursuitDistance	DECIMAL(10,4),	
	mPursuitDuration	TIME,	
			
	PRIMARY KEY (mPerformanceno),
	INDEX (mSystemno),
	INDEX (mPursuitno)
)			
			
DEFAULT CHARACTER SET utf8			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

// ********************************************************************************			
// DB       balmoral			
// table    pursuit			
// 20230321 1003			
			
$query = "			
			
CREATE TABLE pursuit (			
	mPursuitno	    INT(7)	NOT NULL AUTO_INCREMENT,
	mPursuitText	VARCHAR(50),
			
	PRIMARY KEY (mPursuitno)
)			
			
DEFAULT CHARACTER SET utf8			
COLLATE utf8_general_mysql500_ci;			
";			
$invokeTableList[] = $query;			

?>