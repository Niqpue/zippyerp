

DROP TABLE IF EXISTS erp_account_entry;
CREATE TABLE erp_account_entry (
  entry_id int(11) NOT NULL AUTO_INCREMENT,
  acc_d int(11) NOT NULL,
  acc_c int(11) NOT NULL,
  amount int(11) NOT NULL,
  document_id int(11) NOT NULL,
  comment varchar(255) NOT NULL,
  dtag int(11) DEFAULT NULL,
  ctag int(11) DEFAULT NULL,
  PRIMARY KEY (entry_id),
  INDEX document_id (document_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 120
AVG_ROW_LENGTH = 58
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_account_plan;
CREATE TABLE erp_account_plan (
  acc_code int(16) NOT NULL,
  acc_name varchar(255) NOT NULL,
  acc_pid int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (acc_code)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 44
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_bank;
CREATE TABLE erp_bank (
  bank_id int(11) NOT NULL AUTO_INCREMENT,
  bank_name varchar(255) NOT NULL,
  detail text NOT NULL,
  PRIMARY KEY (bank_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 66
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_contact;
CREATE TABLE erp_contact (
  contact_id int(11) NOT NULL AUTO_INCREMENT,
  firstname varchar(64) NOT NULL,
  middlename varchar(64) DEFAULT NULL,
  lastname varchar(64) NOT NULL,
  email varchar(64) DEFAULT NULL,
  detail text NOT NULL,
  description text DEFAULT NULL,
  customer_id int(11) DEFAULT NULL,
  PRIMARY KEY (contact_id),
  INDEX customer_id (customer_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 21
AVG_ROW_LENGTH = 126
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_customer;
CREATE TABLE erp_customer (
  customer_id int(11) NOT NULL AUTO_INCREMENT,
  customer_name varchar(255) DEFAULT NULL,
  detail text NOT NULL,
  contact_id int(11) DEFAULT 0 COMMENT '>0 - ������� ( ������  ��  �������)',
  cust_type int(1) NOT NULL DEFAULT 1 COMMENT '1 - ����������
2 - ��������
3 - ����������/��������
4 - ��������������
0 - ������ ��������  �����������',
  PRIMARY KEY (customer_id),
  INDEX contact_id (contact_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 17
AVG_ROW_LENGTH = 247
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_customer_activity;
CREATE TABLE erp_customer_activity (
  activity_id int(11) NOT NULL AUTO_INCREMENT,
  customer_id int(11) NOT NULL,
  document_id int(11) NOT NULL,
  amount int(11) NOT NULL,
  PRIMARY KEY (activity_id),
  INDEX customer_id (customer_id, document_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 31
AVG_ROW_LENGTH = 17
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_docrel;
CREATE TABLE erp_docrel (
  doc1 int(11) DEFAULT NULL,
  doc2 int(11) DEFAULT NULL,
  INDEX doc1 (doc1),
  INDEX doc2 (doc2)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 9
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_document;
CREATE TABLE erp_document (
  document_id int(11) NOT NULL AUTO_INCREMENT,
  document_number varchar(45) NOT NULL,
  document_date date NOT NULL,
  created datetime NOT NULL,
  updated datetime NOT NULL,
  user_id int(11) NOT NULL,
  notes text DEFAULT NULL,
  content text DEFAULT NULL,
  amount int(11) DEFAULT NULL,
  type_id int(11) NOT NULL,
  state tinyint(4) NOT NULL,
  intattr1 int(11) DEFAULT NULL,
  intattr2 int(11) DEFAULT NULL,
  strattr varchar(255) DEFAULT NULL,
  PRIMARY KEY (document_id),
  INDEX document_date (document_date)
)
ENGINE = MYISAM
AUTO_INCREMENT = 79
AVG_ROW_LENGTH = 483
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_document_update_log;
CREATE TABLE erp_document_update_log (
  document_update_log_id int(11) NOT NULL AUTO_INCREMENT,
  hostname varchar(128) DEFAULT NULL,
  document_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  document_state tinyint(4) NOT NULL,
  updatedon datetime NOT NULL,
  PRIMARY KEY (document_update_log_id),
  INDEX document_id (document_id),
  INDEX user_id (user_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 347
AVG_ROW_LENGTH = 36
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_files;
CREATE TABLE erp_files (
  file_id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) DEFAULT NULL,
  filename varchar(255) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  item_type int(11) NOT NULL,
  PRIMARY KEY (file_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 12
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_filesdata;
CREATE TABLE erp_filesdata (
  file_id int(11) DEFAULT NULL,
  filedata longblob DEFAULT NULL,
  UNIQUE INDEX file_id (file_id)
)
ENGINE = MYISAM
CHARACTER SET utf8
COLLATE utf8_general_ci
ROW_FORMAT = DYNAMIC;

DROP TABLE IF EXISTS erp_item;
CREATE TABLE erp_item (
  item_id int(11) NOT NULL AUTO_INCREMENT,
  itemname varchar(64) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  measure_id varchar(32) DEFAULT NULL,
  item_type tinyint(4) DEFAULT NULL,
  group_id int(11) DEFAULT NULL,
  detail text NOT NULL COMMENT '����  ���   ������',
  PRIMARY KEY (item_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 12
AVG_ROW_LENGTH = 69
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_item_group;
CREATE TABLE erp_item_group (
  group_id int(11) NOT NULL AUTO_INCREMENT,
  group_name varchar(255) NOT NULL,
  PRIMARY KEY (group_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 6
AVG_ROW_LENGTH = 46
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_item_measures;
CREATE TABLE erp_item_measures (
  measure_id int(11) NOT NULL AUTO_INCREMENT,
  measure_name varchar(64) NOT NULL,
  PRIMARY KEY (measure_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 3
AVG_ROW_LENGTH = 20
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_message;
CREATE TABLE erp_message (
  message_id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT NULL,
  created datetime DEFAULT NULL,
  message text DEFAULT NULL,
  item_id int(11) NOT NULL,
  item_type int(11) DEFAULT NULL,
  PRIMARY KEY (message_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 37
AVG_ROW_LENGTH = 40
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_metadata;
CREATE TABLE erp_metadata (
  meta_id int(11) NOT NULL AUTO_INCREMENT,
  meta_type tinyint(11) NOT NULL,
  description varchar(255) DEFAULT NULL,
  meta_name varchar(255) NOT NULL,
  menugroup varchar(255) DEFAULT NULL,
  notes text NOT NULL,
  disabled tinyint(4) NOT NULL,
  PRIMARY KEY (meta_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 53
AVG_ROW_LENGTH = 71
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_metadata_access;
CREATE TABLE erp_metadata_access (
  metadata_access_id int(11) NOT NULL AUTO_INCREMENT,
  metadata_id int(11) NOT NULL,
  role_id int(11) NOT NULL,
  viewacc tinyint(1) NOT NULL DEFAULT 0,
  editacc tinyint(1) NOT NULL DEFAULT 0,
  deleteacc tinyint(1) NOT NULL DEFAULT 0,
  execacc tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (metadata_access_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 9
AVG_ROW_LENGTH = 17
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_moneyfunds;
CREATE TABLE erp_moneyfunds (
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(64) NOT NULL,
  bank int(11) NOT NULL,
  bankaccount varchar(32) NOT NULL,
  ftype smallint(6) NOT NULL COMMENT '0 �����,  1 - ��������  ����, 2 -  ��������������  ����',
  PRIMARY KEY (id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 50
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_moneyfunds_activity;
CREATE TABLE erp_moneyfunds_activity (
  activity_id int(11) NOT NULL AUTO_INCREMENT,
  id_moneyfund int(11) NOT NULL,
  document_id int(11) NOT NULL,
  amount int(11) NOT NULL,
  PRIMARY KEY (activity_id),
  INDEX document_id (document_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 66
AVG_ROW_LENGTH = 17
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_staff_department;
CREATE TABLE erp_staff_department (
  department_id int(11) NOT NULL AUTO_INCREMENT,
  department_name varchar(100) NOT NULL,
  PRIMARY KEY (department_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 32
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_staff_employee;
CREATE TABLE erp_staff_employee (
  employee_id int(11) NOT NULL AUTO_INCREMENT,
  position_id int(11) NOT NULL,
  department_id int(11) NOT NULL,
  salary_type int(11) NOT NULL,
  salary int(11) NOT NULL,
  hireday date DEFAULT NULL,
  fireday date DEFAULT NULL,
  login varchar(64) DEFAULT NULL,
  contact_id int(11) NOT NULL COMMENT '���. ����',
  PRIMARY KEY (employee_id),
  INDEX contact_id (contact_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 16
AVG_ROW_LENGTH = 40
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_staff_employee_activity;
CREATE TABLE erp_staff_employee_activity (
  account_id int(11) NOT NULL AUTO_INCREMENT,
  employee_id int(11) NOT NULL,
  document_id int(11) NOT NULL,
  tax_type tinyint(4) NOT NULL,
  amount int(11) NOT NULL,
  PRIMARY KEY (account_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 17
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_staff_position;
CREATE TABLE erp_staff_position (
  position_id int(11) NOT NULL AUTO_INCREMENT,
  position_name varchar(100) NOT NULL,
  PRIMARY KEY (position_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 28
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_stock_activity;
CREATE TABLE erp_stock_activity (
  stock_activity_id int(11) NOT NULL AUTO_INCREMENT,
  stock_id int(11) NOT NULL,
  document_id int(11) NOT NULL,
  qty int(11) NOT NULL,
  PRIMARY KEY (stock_activity_id),
  INDEX document_id (document_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 110
AVG_ROW_LENGTH = 17
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_store;
CREATE TABLE erp_store (
  store_id int(11) NOT NULL AUTO_INCREMENT,
  storename varchar(64) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  store_type tinyint(4) DEFAULT NULL,
  PRIMARY KEY (store_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 15
AVG_ROW_LENGTH = 30
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_store_stock;
CREATE TABLE erp_store_stock (
  stock_id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL,
  partion int(11) DEFAULT NULL,
  store_id int(11) NOT NULL,
  price int(11) DEFAULT NULL,
  closed tinyint(4) DEFAULT 0 COMMENT ' 1 - ��������������  ������',
  PRIMARY KEY (stock_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 28
AVG_ROW_LENGTH = 22
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_store_stock_serials;
CREATE TABLE erp_store_stock_serials (
  stock_serial_id int(11) NOT NULL AUTO_INCREMENT,
  stock_id int(11) NOT NULL,
  serial_number varchar(255) NOT NULL,
  PRIMARY KEY (stock_serial_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 4
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_task_project;
CREATE TABLE erp_task_project (
  project_id int(11) NOT NULL AUTO_INCREMENT,
  doc_id int(11) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  start_date date DEFAULT NULL,
  end_date date DEFAULT NULL,
  projectname varchar(255) NOT NULL,
  PRIMARY KEY (project_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 56
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_task_task;
CREATE TABLE erp_task_task (
  task_id int(11) NOT NULL AUTO_INCREMENT,
  project_id int(11) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  start_date date DEFAULT NULL,
  end_date date DEFAULT NULL,
  hours int(11) DEFAULT NULL,
  status tinyint(4) UNSIGNED NOT NULL,
  taskname varchar(255) DEFAULT NULL,
  createdby int(11) DEFAULT NULL,
  assignedto int(11) DEFAULT NULL,
  priority tinyint(4) UNSIGNED DEFAULT NULL,
  updated datetime DEFAULT NULL,
  PRIMARY KEY (task_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 10
AVG_ROW_LENGTH = 80
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS erp_task_task_emp;
CREATE TABLE erp_task_task_emp (
  task_emp_id int(11) NOT NULL AUTO_INCREMENT,
  task_id int(11) NOT NULL,
  employee_id int(11) NOT NULL,
  PRIMARY KEY (task_emp_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '  ';

DROP TABLE IF EXISTS system_options;
CREATE TABLE system_options (
  optname varchar(64) NOT NULL,
  optvalue text NOT NULL,
  UNIQUE INDEX optname (optname)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 129
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS system_roles;
CREATE TABLE system_roles (
  role_id int(11) NOT NULL AUTO_INCREMENT,
  rolename varchar(64) NOT NULL,
  description varchar(255) NOT NULL,
  PRIMARY KEY (role_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 2
AVG_ROW_LENGTH = 40
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS system_session;
CREATE TABLE system_session (
  sesskey varchar(64) NOT NULL DEFAULT '',
  expiry timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  expireref varchar(250) DEFAULT '',
  created timestamp DEFAULT '0000-00-00 00:00:00',
  modified timestamp DEFAULT '0000-00-00 00:00:00',
  sessdata longtext DEFAULT NULL,
  PRIMARY KEY (sesskey),
  INDEX sess2_expireref (expireref),
  INDEX sess2_expiry (expiry)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 12992
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS system_user_role;
CREATE TABLE system_user_role (
  role_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  UNIQUE INDEX role_id (role_id, user_id)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 9
CHARACTER SET utf8
COLLATE utf8_general_ci;

DROP TABLE IF EXISTS system_users;
CREATE TABLE system_users (
  user_id int(11) NOT NULL AUTO_INCREMENT,
  userlogin varchar(32) NOT NULL,
  userpass varchar(255) NOT NULL,
  createdon date NOT NULL,
  active int(1) NOT NULL DEFAULT 0,
  email varchar(255) DEFAULT NULL,
  PRIMARY KEY (user_id),
  UNIQUE INDEX userlogin (userlogin)
)
ENGINE = MYISAM
AUTO_INCREMENT = 4
AVG_ROW_LENGTH = 30
CHARACTER SET utf8
COLLATE utf8_general_ci;





--
-- ������ ������������ Devart dbForge Studio for MySQL, ������ 6.2.280.0
-- �������� �������� ��������: http://www.devart.com/ru/dbforge/mysql/studio
-- ���� �������: 17.11.2014 22:08:17
-- ������ �������: 5.1.41-community
-- ������ �������: 4.1
--


--
-- �������� ��� ������������� erp_contact_view
--
CREATE OR REPLACE

VIEW erp_contact_view
AS
SELECT
  `erp_contact`.`contact_id` AS `contact_id`,
  `erp_contact`.`firstname` AS `firstname`,
  `erp_contact`.`middlename` AS `middlename`,
  `erp_contact`.`lastname` AS `lastname`,
  CONCAT_WS(' ', `erp_contact`.`lastname`, `erp_contact`.`firstname`, `erp_contact`.`middlename`) AS `fullname`,
  `erp_contact`.`email` AS `email`,
  `erp_contact`.`detail` AS `detail`,
  COALESCE(`e`.`employee_id`, 0) AS `employee`,
  COALESCE(`cc`.`customer_id`, 0) AS `customer`,
  `erp_contact`.`description` AS `description`,
  `cc`.`customer_name` AS `customer_name`
FROM ((`erp_contact`
  LEFT JOIN `erp_staff_employee` `e`
    ON ((`erp_contact`.`contact_id` = `e`.`contact_id`)))
  LEFT JOIN `erp_customer` `cc`
    ON ((`erp_contact`.`customer_id` = `cc`.`customer_id`)));

--
-- �������� ��� ������������� erp_customer_view
--
CREATE OR REPLACE

VIEW erp_customer_view
AS
SELECT
  `c`.`customer_id` AS `customer_id`,
  `c`.`customer_name` AS `customer_name`,
  `c`.`detail` AS `detail`,
  (SELECT
      COALESCE(SUM(`a`.`amount`), 0) AS `amount`
    FROM `erp_customer_activity` `a`
    WHERE (`c`.`customer_id` = `a`.`customer_id`)) AS `amount`,
  `c`.`cust_type` AS `cust_type`,
  `c`.`contact_id` AS `contact_id`
FROM `erp_customer` `c`;

--
-- �������� ��� ������������� erp_document_view
--
CREATE OR REPLACE

VIEW erp_document_view
AS
SELECT
  `d`.`document_id` AS `document_id`,
  `d`.`document_number` AS `document_number`,
  `d`.`document_date` AS `document_date`,
  `d`.`created` AS `created`,
  `d`.`updated` AS `updated`,
  `d`.`user_id` AS `user_id`,
  `d`.`notes` AS `notes`,
  `d`.`content` AS `content`,
  `d`.`amount` AS `amount`,
  `d`.`type_id` AS `type_id`,
  `d`.`intattr1` AS `intattr1`,
  `d`.`intattr2` AS `intattr2`,
  `d`.`strattr` AS `strattr`,
  `u`.`userlogin` AS `userlogin`,
  `d`.`state` AS `state`,
  `erp_metadata`.`meta_name` AS `meta_name`,
  `erp_metadata`.`description` AS `meta_desc`
FROM ((`erp_document` `d`
  JOIN `system_users` `u`
    ON ((`d`.`user_id` = `u`.`user_id`)))
  JOIN `erp_metadata`
    ON ((`erp_metadata`.`meta_id` = `d`.`type_id`)));

--
-- �������� ��� ������������� erp_item_view
--
CREATE OR REPLACE

VIEW erp_item_view
AS
SELECT
  `t`.`item_id` AS `item_id`,
  `t`.`detail` AS `detail`,
  `t`.`itemname` AS `itemname`,
  `t`.`description` AS `description`,
  `t`.`measure_id` AS `measure_id`,
  `m`.`measure_name` AS `measure_name`,
  `t`.`item_type` AS `item_type`,
  `t`.`group_id` AS `group_id`,
  `g`.`group_name` AS `group_name`
FROM ((`erp_item` `t`
  JOIN `erp_item_measures` `m`
    ON ((`t`.`measure_id` = `m`.`measure_id`)))
  LEFT JOIN `erp_item_group` `g`
    ON ((`t`.`group_id` = `g`.`group_id`)));

--
-- �������� ��� ������������� erp_message_view
--
CREATE OR REPLACE

VIEW erp_message_view
AS
SELECT
  `erp_message`.`message_id` AS `message_id`,
  `erp_message`.`user_id` AS `user_id`,
  `erp_message`.`created` AS `created`,
  `erp_message`.`message` AS `message`,
  `erp_message`.`item_id` AS `item_id`,
  `erp_message`.`item_type` AS `item_type`,
  `system_users`.`userlogin` AS `userlogin`
FROM (`erp_message`
  JOIN `system_users`
    ON ((`erp_message`.`user_id` = `system_users`.`user_id`)));

--
-- �������� ��� ������������� erp_metadata_access_view
--
CREATE OR REPLACE

VIEW erp_metadata_access_view
AS
SELECT
  `a`.`viewacc` AS `viewacc`,
  `a`.`editacc` AS `editacc`,
  `a`.`deleteacc` AS `deleteacc`,
  `a`.`execacc` AS `execacc`,
  `r`.`user_id` AS `user_id`,
  `m`.`meta_type` AS `meta_type`,
  `m`.`meta_name` AS `meta_name`
FROM ((`erp_metadata_access` `a`
  JOIN `system_user_role` `r`
    ON ((`a`.`role_id` = `r`.`role_id`)))
  JOIN `erp_metadata` `m`
    ON ((`a`.`metadata_id` = `m`.`meta_id`)));

--
-- �������� ��� ������������� erp_staff_employee_view
--
CREATE OR REPLACE

VIEW erp_staff_employee_view
AS
SELECT
  `e`.`employee_id` AS `employee_id`,
  `e`.`position_id` AS `position_id`,
  `e`.`department_id` AS `department_id`,
  `e`.`salary_type` AS `salary_type`,
  `e`.`salary` AS `salary`,
  `e`.`hireday` AS `hireday`,
  `e`.`fireday` AS `fireday`,
  `e`.`login` AS `login`,
  `c`.`firstname` AS `firstname`,
  `c`.`lastname` AS `lastname`,
  `c`.`middlename` AS `middlename`,
  `d`.`department_name` AS `department_name`,
  `p`.`position_name` AS `position_name`,
  `e`.`contact_id` AS `contact_id`,
  CONCAT_WS(' ', `c`.`lastname`, `c`.`firstname`, `c`.`middlename`) AS `fullname`,
  CONCAT_WS(' ', `c`.`lastname`, `c`.`firstname`) AS `shortname`
FROM (((`erp_staff_employee` `e`
  JOIN `erp_contact` `c`
    ON ((`e`.`contact_id` = `c`.`contact_id`)))
  LEFT JOIN `erp_staff_position` `p`
    ON ((`e`.`position_id` = `p`.`position_id`)))
  LEFT JOIN `erp_staff_department` `d`
    ON ((`e`.`department_id` = `d`.`department_id`)));

--
-- �������� ��� ������������� erp_stock_activity_view
--
CREATE OR REPLACE

VIEW erp_stock_activity_view
AS
SELECT
  `erp_stock_activity`.`stock_activity_id` AS `stock_activity_id`,
  `erp_stock_activity`.`stock_id` AS `stock_id`,
  `erp_stock_activity`.`qty` AS `quantity`,
  `erp_document`.`document_date` AS `updated`,
  `erp_document`.`document_id` AS `document_id`,
  `erp_store_stock`.`store_id` AS `store_id`,
  `erp_store_stock`.`item_id` AS `item_id`,
  `erp_store_stock`.`partion` AS `partion`,
  `erp_document`.`document_number` AS `document_number`,
  `erp_document`.`document_date` AS `document_date`
FROM ((`erp_stock_activity`
  JOIN `erp_store_stock`
    ON ((`erp_stock_activity`.`stock_id` = `erp_store_stock`.`stock_id`)))
  JOIN `erp_document`
    ON ((`erp_stock_activity`.`document_id` = `erp_document`.`document_id`)));

--
-- �������� ��� ������������� erp_task_project_view
--
CREATE OR REPLACE

VIEW erp_task_project_view
AS
SELECT
  `erp_task_project`.`project_id` AS `project_id`,
  `erp_task_project`.`doc_id` AS `doc_id`,
  `erp_task_project`.`description` AS `description`,
  `erp_task_project`.`start_date` AS `start_date`,
  `erp_task_project`.`end_date` AS `end_date`,
  `erp_task_project`.`projectname` AS `projectname`,
  1 AS `taskall`,
  0 AS `taskclosed`
FROM `erp_task_project`;

--
-- �������� ��� ������������� erp_account_entry_view
--
CREATE OR REPLACE

VIEW erp_account_entry_view
AS
SELECT
  `e`.`entry_id` AS `entry_id`,
  `e`.`acc_d` AS `acc_d`,
  `e`.`acc_c` AS `acc_c`,
  `e`.`amount` AS `amount`,
  `e`.`document_id` AS `document_id`,
  `e`.`comment` AS `comment`,
  `doc`.`document_number` AS `document_number`,
  `doc`.`meta_desc` AS `meta_desc`,
  `doc`.`type_id` AS `type_id`,
  `doc`.`document_date` AS `created`,
  `e`.`dtag` AS `dtag`,
  `e`.`ctag` AS `ctag`
FROM (`erp_account_entry` `e`
  JOIN `erp_document_view` `doc`
    ON ((`e`.`document_id` = `doc`.`document_id`)));

--
-- �������� ��� ������������� erp_stock_view
--
CREATE OR REPLACE

VIEW erp_stock_view
AS
SELECT
  `erp_store_stock`.`stock_id` AS `stock_id`,
  `erp_store_stock`.`item_id` AS `item_id`,
  `erp_item_view`.`itemname` AS `itemname`,
  `erp_store`.`storename` AS `storename`,
  `erp_store`.`store_id` AS `store_id`,
  `erp_item_view`.`measure_name` AS `measure_name`,
  `erp_store_stock`.`price` AS `price`,
  `erp_store_stock`.`partion` AS `partion`,
  `erp_store_stock`.`closed` AS `closed`,
  `erp_item_view`.`item_type` AS `item_type`
FROM ((`erp_store_stock`
  JOIN `erp_item_view`
    ON ((`erp_store_stock`.`item_id` = `erp_item_view`.`item_id`)))
  JOIN `erp_store`
    ON ((`erp_store_stock`.`store_id` = `erp_store`.`store_id`)));

--
-- �������� ��� ������������� erp_task_task_view
--
CREATE OR REPLACE

VIEW erp_task_task_view
AS
SELECT
  `t`.`task_id` AS `task_id`,
  `t`.`project_id` AS `project_id`,
  `t`.`description` AS `description`,
  `t`.`start_date` AS `start_date`,
  `t`.`end_date` AS `end_date`,
  `t`.`hours` AS `hours`,
  `t`.`status` AS `status`,
  `t`.`taskname` AS `taskname`,
  `t`.`createdby` AS `createdby`,
  `t`.`assignedto` AS `assignedto`,
  `t`.`priority` AS `priority`,
  `t`.`updated` AS `updated`,
  `u`.`userlogin` AS `creatwedbyname`,
  CONCAT_WS(' ', `a`.`lastname`, `a`.`firstname`) AS `assignedtoname`,
  `p`.`projectname` AS `projectname`
FROM (((`erp_task_task` `t`
  JOIN `erp_task_project` `p`
    ON ((`t`.`project_id` = `p`.`project_id`)))
  JOIN `system_users` `u`
    ON ((`t`.`createdby` = `u`.`user_id`)))
  LEFT JOIN `erp_staff_employee_view` `a`
    ON ((`t`.`assignedto` = `a`.`employee_id`)));