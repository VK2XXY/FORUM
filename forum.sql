# MySQL dump 5.13
#
# Host: localhost    Database: roma
#--------------------------------------------------------
# Server version	3.22.22

#
# Table structure for table 'forum'
#
CREATE TABLE forum (
  id int(11) DEFAULT '0' NOT NULL auto_increment,
  pid int(11),
  times datetime,
  subj varchar(128),
  author varchar(50),
  email varchar(50),
  content text,
  archive enum('Y','N'),
  level int(11),
  parent enum('Y','N'),
  PRIMARY KEY (id)
);

