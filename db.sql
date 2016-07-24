/**
 * Database creation
 */
create database case_manager;
/**
 * Application Tables
 * @param  {[type]} act_id int(11        [description]
 * @return {[type]}        [description]
 */
create table files(
  act_id int(11) unsigned auto_increment primary key,
  file_id int(11) unsigned default 1000,
  start_date datetime,
  end_date datetime,
  parish int(2) default 1 not null,
  activity int(2) default 0 not null,
  owner_id int(11) default 1000,
  receipt_id int(11) default 1000,
  document_id int(11) default 1000,
  remark varchar(255),
  createdBy int(11) not null,
  created datetime
);
create table owners(
  id int(11) unsigned auto_increment primary key,
  first_name varchar(50),
  last_name varchar(50),
  volume int(5),
  folio int(5),
  act_id int(11)
);

create table receipts (
  id int(11) unsigned auto_increment primary key,
  seen tinyint(2),
  currency_id int(11),
  amount float,
  act_id int(11)
);

create table documents(
  id int(11) unsigned auto_increment primary key,
  act_id int(11),
  comp_agreement tinyint(1),
  sale_agreement tinyint(1),
  cot tinyint(1),
  lease_agreement tinyint(1),
  map tinyint(1),
  property_tax tinyint(1),
  drawing tinyint(1),
  surveyor_id tinyint(1),
  surveyor_report tinyint(1)
);

create table users(
  id int(11) unsigned auto_increment primary key,
  first_name varchar(50) not null,
  last_name varchar(50) not null,
  email varchar(70),
  user_type_id int(11) not null
);

insert into users (first_name, last_name, email, user_type_id) values ("Admin", "Admin", "admin@email.com", 1);
insert into users (first_name, last_name, email, user_type_id) values ("DataEntry", "DataEntry", "dataentry@email.com", 2);
insert into users (first_name, last_name, email, user_type_id) values ("Reviewer", "Reviewer", "reviewer@email.com", 3);

create table usertypes(
  id int(11) unsigned auto_increment primary key,
  title varchar(50) not null,
  description varchar(50)
);

insert into usertypes (title, description) values ("admin", "Admin user");
insert into usertypes (title, description) values ("dataentry", "Data Entry user");
insert into usertypes (title, description) values ("reviewer", "Reviewer user");
/*************************************************/

/*****************
 * System Tables
 ****************/
create table parishes(
  id int(2) unsigned auto_increment primary key,
  iso_code varchar(6) not null,
  title varchar(20) not null
);

insert into parishes (iso_code, title) values("jm-01", "kingston");
insert into parishes (iso_code, title) values("jm-02", "saint andrew");
insert into parishes (iso_code, title) values("jm-03", "saint thomas");
insert into parishes (iso_code, title) values("jm-04", "portland");
insert into parishes (iso_code, title) values("jm-05", "saint mary");
insert into parishes (iso_code, title) values("jm-06", "saint ann");
insert into parishes (iso_code, title) values("jm-07", "trelawny");
insert into parishes (iso_code, title) values("jm-08", "saint james");
insert into parishes (iso_code, title) values("jm-09", "hanover");
insert into parishes (iso_code, title) values("jm-10", "westmoreland");
insert into parishes (iso_code, title) values("jm-11", "saint elizabeth");
insert into parishes (iso_code, title) values("jm-12", "manchester");
insert into parishes (iso_code, title) values("jm-13", "clarendon");
insert into parishes (iso_code, title) values("jm-14", "saint catherine");

create table activities(
  id int(2) unsigned auto_increment primary key,
  title varchar(10) not null,
  description varchar(50)
);

insert into activities (title, description) values ("active", "A file is currently being actioned");
insert into activities (title, description) values ("inactive", "A file is not being actioned");

create table currencies(
    id int(11) unsigned auto_increment primary key,
    iso_code varchar(4) not null,
    country varchar(50) not null
);

insert into currencies (iso_code, country) values ("jmd", "Jamaica Dollar");
insert into currencies (iso_code, country) values ("gbp", "United Kingdom Pound");
/**************************************************************************************/
