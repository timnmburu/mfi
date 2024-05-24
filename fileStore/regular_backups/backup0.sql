DROP TABLE IF EXISTS account;
CREATE TABLE `account` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `custID` text NOT NULL,
  `custName` text NOT NULL,
  `custPhone` text NOT NULL,
  `subDate` text NOT NULL,
  `subAmount` text NOT NULL,
  `lastPayAmount` text NOT NULL,
  `lastPayDate` text NOT NULL,
  `nextPayDate` text NOT NULL,
  `status` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO account VALUES ('1',' 2301','Demo','0725887269','2023-10-01','2000','2000','2023-10-24','2023-11-01','full payment');
INSERT INTO account VALUES ('2',' 2301','Demo','0725887269','2023-10-01','2000','2000','2023-10-24','2023-11-01','full payment');
INSERT INTO account VALUES ('3',' 2301','Demo','0725887269','2023-10-01','','2000','2023-10-24','2023-11-01','full payment');
INSERT INTO account VALUES ('4',' 2301','Demo','0725887269','2023-11-01','2000','','','2023-11-01','');
DROP TABLE IF EXISTS bookings;
CREATE TABLE `bookings` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `bookingID` text NOT NULL,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `email` text NOT NULL,
  `services` text NOT NULL,
  `description` text DEFAULT NULL,
  `dateBooked` text NOT NULL,
  `quote` text NOT NULL,
  `dateRequested` text NOT NULL,
  `amountDue` text DEFAULT NULL,
  `depositCode` text DEFAULT NULL,
  `depositPaid` text DEFAULT '0',
  `totalPaid` text DEFAULT '0',
  `balanceDue` text DEFAULT NULL,
  `lastPaymentDate` text DEFAULT NULL,
  `confirmation` text NOT NULL DEFAULT 'Unconfirmed',
  `status` text NOT NULL DEFAULT 'Pending Payment',
  `invoiceLink` text DEFAULT NULL,
  `contractLink` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO bookings VALUES ('1','20230809121533','Asumpta Kimosop','0780714***','vwkimti@gmail.com','bridal make up, pedicure and manicure','bridal make up, pedicure and manicure, hair styling and transport ','2023-08-24','14:00','2023-08-09 12:15:33','6800','KCB','3800','3800','3000','2023-08-07','Confirmed','Deposit Paid','fileStore/booking_invoices/INV_20230809121533.pdf','/fileStore/booking_contracts/CONT_20230809121533.pdf');
INSERT INTO bookings VALUES ('2','20230809154135','Hannah Wanguti','0726905***','hantush@gmail.com','Ultimate without lashes ','Manicure, Pedicure, Ultimate makeup (No Lashes)','2023-08-24','14:00','2023-08-09 15:41:35','2800','','0','0','2800','','Confirmed','Pending Payment','fileStore/booking_invoices/INV_20230809154135.pdf','/fileStore/booking_contracts/CONT_20230809154135.pdf');
INSERT INTO bookings VALUES ('3','20230810145536','Maritab  Giocho ','0729154***','mgireset@gmail.com','Bridal','Manicure, Pedicure, Ultimate makeup (No Lashes)','2023-08-24','14:00','2023-08-10 14:55:36','2800','Other','2800','2800','0','2023-08-10','Confirmed','Payment Completed','fileStore/booking_invoices/INV_20230810145536.pdf','/fileStore/booking_contracts/CONT_20230810145536.pdf');
INSERT INTO bookings VALUES ('4','20230811213632','Wanjiku Asunda','0723542***','symkid@yahoo.com','Bridal makeup ','Manicure, Pedicure, Ultimate makeup (No Lashes)','2023-08-25','07:45','2023-08-11 21:36:32','2800','','0','0','2800','','Confirmed','Pending Payment','fileStore/booking_invoices/INV_20230811213632.pdf','/fileStore/booking_contracts/CONT_20230811213632.pdf');
INSERT INTO bookings VALUES ('5','20230812232528','Sylvester karungi','0785777***','sylkaru@ymail.com','Bridal makeup','It will be done','2023-08-24','14:00','2023-08-12 23:25:28','5000','Other','2000','5000','0','2023-08-17','Confirmed','Payment Completed','fileStore/booking_invoices/INV_20230812232528.pdf','fileStore/booking_contracts/CONT_20230812232528.pdf');
INSERT INTO bookings VALUES ('12','20230821121607','Temeza Butchery','254**3747172','butchtemeza@hotmail.com','System','','2023-09-01','2838247','2023-08-21 12:16:07','','','0','0','','','Unconfirmed','Pending Payment','','');
INSERT INTO bookings VALUES ('13','20230821121734','Salon City','25484***2','citysalon@salon.com','Sytem and website','','2023-09-09','2342341','2023-08-21 12:17:34','','','0','0','','','Unconfirmed','Pending Payment','fileStore/booking_invoices/INV_20230821121734.pdf','fileStore/booking_contracts/CONT_20230821121734.pdf');
DROP TABLE IF EXISTS bookings1;
CREATE TABLE `bookings1` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `bookingID` text NOT NULL,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `email` text NOT NULL,
  `services` text NOT NULL,
  `description` text DEFAULT NULL,
  `dateBooked` text NOT NULL,
  `time` text NOT NULL,
  `dateRequested` text NOT NULL,
  `amountDue` text DEFAULT NULL,
  `depositCode` text DEFAULT NULL,
  `depositPaid` text DEFAULT '0',
  `totalPaid` text DEFAULT '0',
  `balanceDue` text DEFAULT NULL,
  `lastPaymentDate` text DEFAULT NULL,
  `confirmation` text NOT NULL DEFAULT 'Unconfirmed',
  `status` text NOT NULL DEFAULT 'Pending Confirmation',
  `invoiceLink` text DEFAULT NULL,
  `contractLink` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO bookings1 VALUES ('1','20230809121533','Veronica Kimere','0725887269','vwkimere@gmail.com','bridal make up, pedicure and manicure','bridal make up, pedicure and manicure, hair styling and transport ','2023-08-24','14:00','2023-08-09 12:15:33','6800','KCB','3800','0','1500','2023-08-09','Confirmed','Pending Confirmation','fileStore/booking_invoices/INV_20230809121533.pdf','/fileStore/booking_contracts/CONT_20230809121533.pdf');
INSERT INTO bookings1 VALUES ('2','20230809154135','Ann Wangari','0733440443','wangtush@gmail.com','Ultimate without lashes ','','2023-08-24','14:00','2023-08-09 15:41:35','2800','','0','0','2800','','Confirmed','Pending Payment','fileStore/booking_invoices/INV_20230809154135.pdf','/fileStore/booking_contracts/CONT_20230809154135.pdf');
DROP TABLE IF EXISTS commission_payments;
CREATE TABLE `commission_payments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `amount` text NOT NULL,
  `accBal` text NOT NULL,
  `date` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO commission_payments VALUES ('1','Annita Nyeu','254725887***','6830','3200.99','2023-06-17 20:48:27');
INSERT INTO commission_payments VALUES ('2','Absolom Njeri','2547200992***','3330','8760.79','2023-06-24 20:13:21');
INSERT INTO commission_payments VALUES ('3','Direst Dhana','254724503***','5250','3410.79','2023-06-24 20:23:57');
INSERT INTO commission_payments VALUES ('4','Annita Nyeu','254722519***','4380','6450.79','2023-07-01 20:04:43');
INSERT INTO commission_payments VALUES ('5','Direst Dhana','0797567***','11030','10600.79','2023-07-01 21:21:20');
INSERT INTO commission_payments VALUES ('6','Direst Dhana','0796567***','7880','1150.04','2023-07-08 19:16:53');
INSERT INTO commission_payments VALUES ('13','demo','0725887269','10','3746.99','2023-09-29 09:25:06');
INSERT INTO commission_payments VALUES ('14','demo','254725887269','12','2791.39','2023-10-04 10:06:53');
INSERT INTO commission_payments VALUES ('15','demo','254725887269','12','2759.83','2023-10-04 15:21:02');
DROP TABLE IF EXISTS customers;
CREATE TABLE `customers` (
  `custID` int(11) NOT NULL AUTO_INCREMENT,
  `custName` text NOT NULL,
  `custPhone` text NOT NULL,
  `points` int(11) DEFAULT NULL,
  `redeemed` int(11) NOT NULL DEFAULT 0,
  `lastRedeemed` timestamp NULL DEFAULT NULL,
  `pointsBal` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`custID`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO customers VALUES ('1','Hill ','0791606***','110','0','0000-00-00 00:00:00','110');
INSERT INTO customers VALUES ('2','Bernardo','0722512***','40','0','0000-00-00 00:00:00','40');
INSERT INTO customers VALUES ('3','Delici','0700227***','60','0','0000-00-00 00:00:00','60');
INSERT INTO customers VALUES ('4','Lizzie','0704895***','90','0','0000-00-00 00:00:00','90');
INSERT INTO customers VALUES ('5','Merceille','0798166***','410','0','0000-00-00 00:00:00','410');
INSERT INTO customers VALUES ('6','Mundo','0728113***','50','0','0000-00-00 00:00:00','50');
INSERT INTO customers VALUES ('7','Bret','0701453***','110','0','0000-00-00 00:00:00','110');
INSERT INTO customers VALUES ('8','Hanasia','0799047***','310','0','0000-00-00 00:00:00','310');
INSERT INTO customers VALUES ('84','Tammy','0725887269','30','0','0000-00-00 00:00:00','30');
INSERT INTO customers VALUES ('85','Sylvester karungi','00785777','50','0','0000-00-00 00:00:00','50');
INSERT INTO customers VALUES ('86','Tim','0725882769','3','0','','3');
DROP TABLE IF EXISTS expenseHistory;
CREATE TABLE `expenseHistory` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `amount` text NOT NULL,
  `date` text NOT NULL,
  `currentTotal` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO expenseHistory VALUES ('1','Rent','5000','2023-07-24 19:28:21','5000');
INSERT INTO expenseHistory VALUES ('2','Wifi','1000','2023-07-24 19:28:43','6000');
INSERT INTO expenseHistory VALUES ('3','Electricity','500','2023-07-24 19:30:28','6500');
INSERT INTO expenseHistory VALUES ('4','Salary','5000','2023-07-24 19:30:41','11500');
INSERT INTO expenseHistory VALUES ('5','Misc Expenses','3000','2023-07-25 09:49:57','14500');
INSERT INTO expenseHistory VALUES ('6','Misc Expenses','3000','2023-07-27 10:04:06','14500');
DROP TABLE IF EXISTS expenses;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `price` decimal(11,0) NOT NULL,
  `quantity` text NOT NULL,
  `date` text NOT NULL,
  `paidFrom` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO expenses VALUES ('1','Banner ground','5000','1','2023-05-10','KCB Paybill');
INSERT INTO expenses VALUES ('2','Teepee Pegs','1550','1','2023-04-07','KCB Paybill');
INSERT INTO expenses VALUES ('3','Garnier Even & Matte Cream SPF 30 50ml','10500','1','2023-04-07','KCB Paybill');
INSERT INTO expenses VALUES ('4','Imp Leather Japanese Spa Lotion 400ml ','4500','1','2023-04-07','KCB Paybill');
INSERT INTO expenses VALUES ('5','Banner roof print','1000','1','2023-04-23','KCB Paybill');
INSERT INTO expenses VALUES ('6','Teepee nylon rope','860','1','2023-04-07','KCB Paybill');
INSERT INTO expenses VALUES ('7','Skytone soap box ','1040','1','2023-04-07','KCB Paybill');
INSERT INTO expenses VALUES ('8','H&B Face towel bright red','1150','1','2023-04-07','KCB Paybill');
INSERT INTO expenses VALUES ('9','Aluminum case LED downlighters','1820','7','2023-04-24','KCB Paybill');
INSERT INTO expenses VALUES ('10','CCTV Bulb PTZ camera','2500','1','2023-04-06','KCB Paybill');
INSERT INTO expenses VALUES ('11','Henna','4000','4','2023-06-17','KCB Paybill');
INSERT INTO expenses VALUES ('12','Annita Nyea 254701519*** Commission','6830','1','2023-06-17 20:48:27','Mpesa Online');
INSERT INTO expenses VALUES ('13','Aprons','7800','6','2023-06-24','KCB Paybill');
DROP TABLE IF EXISTS expenses1;
CREATE TABLE `expenses1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `price` decimal(11,0) NOT NULL,
  `quantity` text NOT NULL,
  `date` text NOT NULL,
  `paidFrom` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
DROP TABLE IF EXISTS feedback;
CREATE TABLE `feedback` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `comment` text NOT NULL,
  `date` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO feedback VALUES ('8','Mike Smith
','mikeEndonormaPsymn@gmail.com','If you are looking to rank your local business on Google Maps in a specific area, this service is for you. 
 
Google Map Stacking is a highly effective technique for ranking your GMB within a specific mile radius. 
 
More info: 
https://www.speed-seo.net/product/google-maps-pointers/ 
 
Thanks and Regards 
Mike Smith
 
 
PS: Want a comprehensive local plan that covers everything? 
https://www.speed-seo.net/product/local-seo-bundle/','2023-07-09 07:15:58');
INSERT INTO feedback VALUES ('9','StephenUrgef','streetwambui@gmail.com','Impressed with your brand and online presence. Wambui Street offers competitive loans for companies. We specialize in scaling businesses with good rates. Boost your credit for easier loan qualification. Apply for up to $25M same-day approval. Free consultation. Schedule a Zoom call: https://calendly.com/wambuistreet/meeting-with-wambui-kinuthia or fill the form: https://www.wambuistreet.com/. Looking forward to hearing from you. Wambui Kinuthia, CEO, Wambui Street','2023-07-11 10:16:04');
INSERT INTO feedback VALUES ('22','Mike Nash
','mikePsymn@gmail.com','Hi there 
 
Just checked your essentialtech.site backlink profile, I noticed a moderate percentage of toxic links pointing to your website 
 
We will investigate each link for its toxicity and perform a professional clean up for you free of charge. 
 
Start recovering your ranks today: 
https://www.hilkom-digital.de/professional-linksprofile-clean-up-service/ 
 
 
Regards 
Mike Nash
Hilkom Digital SEO Experts 
https://www.hilkom-digital.de/','2023-08-12 14:35:05');
INSERT INTO feedback VALUES ('23','Tim','miltim##43@gmail.com','This is a very good system.','2023-08-14');
DROP TABLE IF EXISTS frequentPayments;
CREATE TABLE `frequentPayments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `wallet` text NOT NULL,
  `reference` text NOT NULL,
  `account` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO frequentPayments VALUES ('1','Trizah Wendy','Mpesa','mpesa','0725678***');
INSERT INTO frequentPayments VALUES ('2','Textiles Industry','Paybill','475836**','8293949**');
INSERT INTO frequentPayments VALUES ('3','Rent','Bank','Mayfair Bank','28732849***');
INSERT INTO frequentPayments VALUES ('4','Stock','Mpesa Buygoods','Mpesa','4545***');
DROP TABLE IF EXISTS givings;
CREATE TABLE `givings` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `amount` text NOT NULL,
  `narration` text NOT NULL,
  `date` text NOT NULL,
  `status` text NOT NULL DEFAULT 'Not paid',
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO givings VALUES ('1','Alexander','254727751***','3750','Tithe','2023-08-10 23:20:01','Paid');
DROP TABLE IF EXISTS images;
CREATE TABLE `images` (
  `image_path` text NOT NULL,
  `time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO images VALUES ('fileStore/2.jpg','2023-04-17 11:23:23');
INSERT INTO images VALUES ('fileStore/3.jpg','2023-04-17 11:24:36');
INSERT INTO images VALUES ('fileStore/9.jpg','2023-04-17 11:26:50');
INSERT INTO images VALUES ('fileStore/17.jpg','2023-04-17 11:28:32');
INSERT INTO images VALUES ('fileStore/26.jpg','2023-04-17 11:28:32');
INSERT INTO images VALUES ('fileStore/WhatsApp Image 2023-04-10 at 13.00.06 (1).jpeg','2023-04-17 14:15:08');
DROP TABLE IF EXISTS inventory;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `price` decimal(11,0) NOT NULL,
  `quantity` text NOT NULL,
  `date` text NOT NULL,
  `paidFrom` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO inventory VALUES ('1','Teepee Pegs','1550','1','2023-04-07','KCB Paybill');
INSERT INTO inventory VALUES ('2','Garnier Even & Matte Cream SPF 30 50ml','10500','1','2023-04-07','KCB Paybill');
INSERT INTO inventory VALUES ('3','Imp Leather Japanese Spa Lotion 400ml ','4500','1','2023-04-07','KCB Paybill');
INSERT INTO inventory VALUES ('4','Teepee nylon rope','860','1','2023-04-07','KCB Paybill');
INSERT INTO inventory VALUES ('5','Skytone soap box ','1040','1','2023-04-07','KCB Paybill');
INSERT INTO inventory VALUES ('6','H&B Face towel bright red','1150','1','2023-04-07','KCB Paybill');
INSERT INTO inventory VALUES ('7','Henna','4000','4','2023-06-17','KCB Paybill');
INSERT INTO inventory VALUES ('8','Aprons','7800','6','2023-06-24','KCB Paybill');
INSERT INTO inventory VALUES ('9','Nail Drill','32000','1','2023-06-24','KCB Paybill');
INSERT INTO inventory VALUES ('10','Blue sky Base and Top Coat','19000','2','2023-06-24','KCB Paybill');
INSERT INTO inventory VALUES ('11','Nail builder brush','4000','2','2023-06-24','KCB Paybill');
INSERT INTO inventory VALUES ('12','Magic remover','4000','1','2023-06-24','KCB Paybill');
DROP TABLE IF EXISTS mpesa_collections;
CREATE TABLE `mpesa_collections` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` text NOT NULL,
  `state` text NOT NULL,
  `provider` text NOT NULL,
  `charges` text NOT NULL,
  `net_amount` text NOT NULL,
  `value` text NOT NULL,
  `account` text NOT NULL,
  `api_ref` text NOT NULL,
  `clearing_status` text DEFAULT NULL,
  `mpesa_reference` text DEFAULT NULL,
  `failed_reason` text DEFAULT NULL,
  `failed_code` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO mpesa_collections VALUES ('22','0L3JJ9Y','COMPLETE','M-PESA','0.00','10.00','10.00','254725887269','Excel Tech','AVAILABLE','RI51XE4YOR','','');
INSERT INTO mpesa_collections VALUES ('23','Y7MQJE0','COMPLETE','M-PESA','0.00','11.00','11.00','254725887269','Essentialapp','AVAILABLE','RJ45IQFPNN','','');
INSERT INTO mpesa_collections VALUES ('24','Y3BW7EY','COMPLETE','M-PESA','0.00','10.00','10.00','254725887269','Essentialapp','AVAILABLE','RJ46IQRKCI','','');
DROP TABLE IF EXISTS mpesa_payments;
CREATE TABLE `mpesa_payments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `amount` text NOT NULL,
  `accBal` text NOT NULL,
  `date` text NOT NULL,
  `notifyPhone` text DEFAULT NULL,
  `trackingID` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `notified` text NOT NULL DEFAULT '0',
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO mpesa_payments VALUES ('139','demo','0725887269','10','3746.99','2023-09-29 09:25:06','254725887269','0c8887b0-9107-483a-b94a-5de06f900aac','','0');
INSERT INTO mpesa_payments VALUES ('140','demo','254725887269','12','2791.39','2023-10-04 10:06:53','254725887269','86790a59-18c8-45f6-a9aa-1da088fb3333','','0');
INSERT INTO mpesa_payments VALUES ('141','Essentialapp','0725887269','10','2771.39','2023-10-04 10:36:10','254725887269','1be778bc-b321-4f47-8c1c-9b735772afdd','','0');
INSERT INTO mpesa_payments VALUES ('142','Essentialapp','0725887269','10','2751.39','2023-10-04 10:47:35','254725887269','68bfa438-c3ab-4084-b093-88c7503fe3e5','','0');
INSERT INTO mpesa_payments VALUES ('143','Essentialapp','0725887269','10','2781.83','2023-10-04 15:19:27','254725887269','6d5b4961-494c-4535-b2c3-c58d00442edd','','0');
INSERT INTO mpesa_payments VALUES ('144','demo','254725887269','12','2759.83','2023-10-04 15:21:02','254725887269','c758fc69-d526-440b-a54e-81da50a2175b','','0');
DROP TABLE IF EXISTS mpesa_transfers;
CREATE TABLE `mpesa_transfers` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` text NOT NULL,
  `tracking_id` text NOT NULL,
  `status` text NOT NULL,
  `status_code` text NOT NULL,
  `transaction_id` text NOT NULL,
  `transaction_status` text NOT NULL,
  `transaction_status_code` text NOT NULL,
  `provider` text NOT NULL,
  `bank_code` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `account` text DEFAULT NULL,
  `account_type` text DEFAULT NULL,
  `account_reference` text DEFAULT NULL,
  `provider_reference` text DEFAULT NULL,
  `provider_account_name` text DEFAULT NULL,
  `amount` text NOT NULL,
  `charge` text DEFAULT NULL,
  `narrative` text DEFAULT NULL,
  `failed_amount` text DEFAULT NULL,
  `wallet_available_balance` text NOT NULL,
  `updated_at` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO mpesa_transfers VALUES ('8','KZ55ER0','dd5cab5f-fed7-44ff-9825-a8c0ff7b49c5','Completed','BC100','KO6D9J0','Successful','TS100','MPESA-B2B','','Customer','247247','PayBill','0340161447990','RI58X8JLFW','  Equity Paybill Account','10.00','10.00','test 05 September','0','614.65','2023-09-05T08:17:58.051603+03:00');
INSERT INTO mpesa_transfers VALUES ('9','Y7ZZBLK','49c343d8-dee1-47bf-8c8a-50f4ea61d992','Completed','BC100','0WX3Q4K','Unsuccessful','TF106','MPESA-B2C','','','254725887269','','','RI56XH74HS','','1.00','10.00','Excel Tech','1.00','624.35','2023-09-05T09:51:21.361643+03:00');
INSERT INTO mpesa_transfers VALUES ('10','0WXD94K','9119c0da-7c2b-4cfa-aec2-68e9a4a0cc4f','Completed','BC100','Y6XEBD0','Successful','TS100','MPESA-B2C','','','254725887269','','','RI652TDHW7','  TIMOTHY NJOROGE MBURU','10.00','10.00','Excel Tech','0','313.25','2023-09-06T16:47:59.019248+03:00');
INSERT INTO mpesa_transfers VALUES ('11','YD3Q730','2caa7761-e502-4a3c-aa6a-ee821cc9152a','Completed','BC100','0LNELEY','Unsuccessful','TF106','MPESA-B2C','','','254725887269','','','RI682TX3BU','','5.00','10.00','Excel Tech','5.00','313.25','2023-09-06T16:52:37.905018+03:00');
INSERT INTO mpesa_transfers VALUES ('12','0VGE4Z0','68bfa438-c3ab-4084-b093-88c7503fe3e5','Completed','BC100','Y72ROL0','Successful','TS100','MPESA-B2C','','','254725887269','','','RJ46IO9GZE','  TIMOTHY NJOROGE MBURU','10.00','10.00','Essentialapp','0','2751.39','2023-10-04T10:47:11.470802+03:00');
INSERT INTO mpesa_transfers VALUES ('13','0VGE4Z0','68bfa438-c3ab-4084-b093-88c7503fe3e5','Completed','BC100','Y72ROL0','Successful','TS100','MPESA-B2C','','','254725887269','','','RJ46IO9GZE','  TIMOTHY NJOROGE MBURU','10.00','10.00','Essentialapp','0','2751.39','2023-10-04T10:47:11.470802+03:00');
INSERT INTO mpesa_transfers VALUES ('14','YP6E9G0','6d5b4961-494c-4535-b2c3-c58d00442edd','Completed','BC100','0WD8M9Y','Successful','TS100','MPESA-B2C','','','254725887269','','','RJ41JHZ0WX','  TIMOTHY NJOROGE MBURU','10.00','10.00','Essentialapp','0','2781.83','2023-10-04T15:19:37.962387+03:00');
INSERT INTO mpesa_transfers VALUES ('15','KBLV22Y','c758fc69-d526-440b-a54e-81da50a2175b','Completed','BC100','KZ382VK','Successful','TS100','MPESA-B2C','','','254725887269','','','RJ43JI5LFF','  TIMOTHY NJOROGE MBURU','12.00','10.00','Essentialapp','0','2759.83','2023-10-04T15:21:12.888932+03:00');
DROP TABLE IF EXISTS offers;
CREATE TABLE `offers` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `offer_name` text NOT NULL,
  `offer_image_poster` text NOT NULL,
  `start_date` text NOT NULL,
  `end_date` text NOT NULL,
  `status` text NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO offers VALUES ('1','Offer of July 2023','fileStore/Salon Offer thru July 2023.jpg','2023-07-01','2023-07-31','Stopped 2023-08-16');
DROP TABLE IF EXISTS orders;
CREATE TABLE `orders` (
  `order_number` int(11) NOT NULL AUTO_INCREMENT,
  `orderTime` text NOT NULL,
  `custName` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `product` text NOT NULL,
  `quantity` text NOT NULL,
  `delivered` text DEFAULT 'No',
  `country` text NOT NULL,
  `postal_address` text NOT NULL,
  `postal_code` text NOT NULL,
  `location` text NOT NULL,
  PRIMARY KEY (`order_number`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO orders VALUES ('1','2023-06-03 07:01:00','Jasmine Gibb','jasgibb1@gmail.com','9492930072','pd','2','Yes','','','','');
INSERT INTO orders VALUES ('2','2023-06-03 07:01:30','Jasmine Gibb','jasgibb1@gmail.com','9492930072','pd','2','No','','','','');
INSERT INTO orders VALUES ('3','2023-08-31 22:49:57','Katy Kitkat','info@essentialtech.site','25478546**','System','1','No','Kenya','9384','9000','Nairobi');
DROP TABLE IF EXISTS otpQ;
CREATE TABLE `otpQ` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `phone` text NOT NULL,
  `otpHash` text NOT NULL,
  `dateInitiated` text NOT NULL,
  `status` text DEFAULT NULL,
  `dateDelivered` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO otpQ VALUES ('20','254725887269','5yPE32yjB0Lp5eGLg4EwVAo6F0aVqeO9xw4tCgIssuYjPrM7D3NUQVSfdJBteaGKAMhYY9cL7AhJn1KPwqm2NVlLhaw3h5DWEYsUowqswVrKwazsmBSSrUo2hLM7aZBF','2023-09-28 09-35-24','','');
INSERT INTO otpQ VALUES ('21','254725887269','qxe15K7Z8skeeLsGiO1kq/uLWdVHoOjNHVN91D7ozGKP4c7vXy2T1awi8QuELEl1UGJoNQq71ZQUtkJWGqWvFwGsDqTI2PQipPhhw7yING1x2It/+/lMLs+NDjlBjABI','2023-09-28 10-36-11','','');
INSERT INTO otpQ VALUES ('22','254725887269','c90gJHIeszAeQr46VR8thymEMQIAsoA6pclUniLDPtV2JygJ0qpRJG7L7mBCThony28JbgEJOQhzM269AwDBuxKKJc4hPcliuwOUwS7qQaQ7rqPM00P6Uia2IfoHJg9q','2023-09-28 10-47-16','','');
INSERT INTO otpQ VALUES ('23','254725887269','63rVJf2B8R9WRH+STUM5IIhx9irKBue2pZ8dEdDQjhEKlYj/GVCj1rB2T/Di5gr5H2zU2KgQBRk+pFlYZBM9+4g8LSUMIaGHW3G5LpSWiZVwMi9GBXTGVNq1vz+5s9SB','2023-09-28 10-49-52','','');
INSERT INTO otpQ VALUES ('28','254725887269','ASjpTP2zVHbJ26ex4lK8hPkcu3ATqoQxHHniWDWFWnV8qE6Y4K2KJC/R0UJOYFNmEBiWymOEJJj5ZSA4InkYITVY9bTUUlBuHYlrV1xe7lUGXYeraDO+c88jI51d27yv','2023-09-28 11-08-12','','');
INSERT INTO otpQ VALUES ('37','254725887269','7BhFqi7f6VUZ1ObweUv39aIvl2pSDuhcfZoC0VH2nsGWzpuOj1jgHiAaNTEXgZZugetEy4jXILADF24EMau87kX1zcXNlsklgBnHWr6BRmJRgtD1FErbq45d4MzxSnoR','2023-09-28 15:30:31','','');
INSERT INTO otpQ VALUES ('38','254725887269','Bjvphi7p/s05xrCXDegVS+o0BnOGoD6668HD6Lnol1+OA2EIDVPH50HZ/NcpRlZFnMpdDSO28rOzbpVVBbRk2kFw/Kj8QbBQ7Twg3TscHWquYPpiC7gMRtXh+I2rdd4x','2023-09-29 08:37:55','','');
INSERT INTO otpQ VALUES ('39','254725887269','J8l4WPbXBayk6EJioFgNzZSYQ9pUE3RvwSZtfnTY6V73aslm9LmnhGHpBhjJiZYGJeoTiUq38AHONtpi5ohdfBCxcgh1tLbAANbGdNQYO1p4yXk9BLxyavwSM2jdBtX/','2023-09-29 08:40:37','','');
INSERT INTO otpQ VALUES ('40','254725887269','VaQOUHMaLN5/3LR0QqmcGz0iheHAPDPdMkybabbenEdzbNCHSdfZJ/nRNxnm6gTa6XDcaoGSJ7bYePrusfhviRfGnjpr0fR+GibGLXs8tSWLHCJOn2/wflzTR/NoiIge','2023-09-29 08:42:33','','');
INSERT INTO otpQ VALUES ('41','254725887269','c/ricElNDSKNE8rNzB78kedweO2XuGOi+Ttgu5zvmWUN/7em8xuyNaX6Wl2a2LaYtG7yONPUvben9XnpyrD1Ldd6po8QuEm08wKcjP51McsK4KR625x/biT8GvJ11fOZ','2023-09-29 09:13:51','','');
INSERT INTO otpQ VALUES ('42','254725887269','9Q0wLRdmskEvncAXGYYhxrMKjNRtgCzessezQ3hcDBUg5OpegnHi5zJAGljPCTCed0Ugs0D5clnYOxnNvkk6D4aQMXiYQzWMWzT38SUThkSTbg5MpPetI42kijzTh2ft','2023-09-29 09:16:41','','');
INSERT INTO otpQ VALUES ('43','254725887269','NkplKh78jHziDoOJ3gzqZk99TWYZZA2bTmtYQcRu8lVtneBsdm6DAx9ViO9W+1SzB6BUT0hPi21SmJ+O3UNyN9F4aKiBXbNPp9O4F+zi/x9o9lMXWgFNA/J6ByMuWKnM','2023-09-29 09:18:19','','');
INSERT INTO otpQ VALUES ('44','254725887269','WRgHAC0PCwRJy9092LBliZIBw9LwSaxnIsIuu3TRg+XK/1/2oa1sOkFOZ27lpk1q3oGaihkUILdgoJQCzWzvAFZ+nhcSMCGXY9lmVStQgbvZ+4JfRGd0wMTDVVyulULh','2023-09-29 09:24:37','','');
INSERT INTO otpQ VALUES ('45','254725887269','iiBnxoKYTKmvVR8gAlxHXQku72pC+Y9anVa4cT6TieSsMJJtNguNyxLyd//Yr5kudVorcov5fYYoC7YfGI/OAW7MnkQuiPjJidqEiKNXrFuvk+ZSYE3ickhOOQXIPJfR','2023-10-03 12:05:19','','');
INSERT INTO otpQ VALUES ('46','254725887269','S69X7J8Dwebibh6M37eaVqDWGJ8rzmi7WY0yy7k4YpKI+awCAlX2PiAKLNyYA8xehq4hyE+yGKujrTdTqOPjLEiDkaDnWf6hhKAq8ktAd3uSWTYI6hiUE6IiYqn7XIVj','2023-10-03 12:05:21','','');
INSERT INTO otpQ VALUES ('47','254725887269','FoRr/bXSUWZggSqFX3WWWjICMsJl3RhNUjbNZcMmNfDn8Bf6PASJDXzwTdOQzznbwgM1sYZjmMZCqYpGA10SbYkbYAorcp6rW8aGZxvOQoE5FdmPUxspYe1gHHeD+E9q','2023-10-03 12:06:26','','');
INSERT INTO otpQ VALUES ('48','254725887269','rlRwU47lFuKnZHpbBzf21P9dcCzQQUhnHKNvVKWX/GM8PQdHtYpBhFuAZvmr3oU5ui5ufaLOJIe9+3qouv1SyBS+AIkB7H6zYJ3DoR/Z8QjBTu9ceg5foBg/sBoszMbg','2023-10-04 11:54:51','','');
INSERT INTO otpQ VALUES ('49','254725887269','gryuM1LBvsSdhD7Ls2qNg2n8DHyuNBZw5DBt58gYrK5W2f/EecbHTpK7w2B+9ZwSaoEzIduY2aDndA8fp22g7vWcgxjy1SaFg3ASLTGE8c1H2njLFSuh+xkwoZXe6E38','2023-10-04 11:57:54','','');
INSERT INTO otpQ VALUES ('50','254725887269','6j3yWQl10mY7dalKkzSvrEHXOm0T9s6vgQ1sUHzCqc/JEdDt2OgC3gNvApoeh6H0FDinHaufacJVy39KR9iaSUk1yrZ8VrU1PyjbKqDAVlBWApfFgSk5ZUfKm4PG3gHs','2023-10-04 12:02:14','','');
INSERT INTO otpQ VALUES ('51','254725887269','fUMTZzFueb+hSexwr3jo07BzeWdlWqSDn8xozUI8qZrGg+HsN2MtuWyoGY7e0ONNfOPk2GBAL/46Ad9jl288mBLGmsKw1m2uySddCa/qLKWoVIYmU16/dVp7otIsaOyZ','2023-10-04 12:05:30','','');
INSERT INTO otpQ VALUES ('52','254725887269','ZeHWpZmGUz/RbNXevOLpe+XZYywV6rN7VCWGkdymnp8kVXF/qwjL5RFgcJkziK81C6xM+V+bI+2r+rA16VhBOjMcUkwRfldWXBgQ9aIUsrg0yd3cwPOdKH6iWtjMJTA4','2023-10-04 13:04:34','','');
INSERT INTO otpQ VALUES ('53','254725887269','MzdydDsxyRXJWTpqqh9FZ5ym8j6s7Aj2JPhd5B+eqWkcVkfwHbYR9S6P+ZBMEwTaawOvTNeHi0BtyQn1oAJ5D8VE3PTLZ1H9Al/u3mDWDXDCG6PxGRuCc/yQ6sBD6MPl','2023-10-04 13:06:21','','');
INSERT INTO otpQ VALUES ('54','254725887269','VOKuLEOpXWNYq+S7yHk3Lq75AH7FWGuPjRLAcflsPpyzbCDZZ5R8u4NIKHgEWivPcJ2Natb/GbY6x/fSKxNokg5AOs4UQ214j3wJg1mlLRij3/+aatnnVRWO4RXH61cq','2023-10-04 13:35:19','','');
INSERT INTO otpQ VALUES ('55','254725887269','B5gHngueCDT0Xdyw8OddwgnkGYN3iuApQqpGQrcw5ZWVebuxQ1RYuJ5/YMNRmo8kocWAeKp0R9hXzdUrbCHwDqtfVdF94FgrAnrkV8k4zN6ADWfVwiyBFvuSEI9SEbfj','2023-10-04 13:45:21','','');
INSERT INTO otpQ VALUES ('56','254725887269','2CFexbmV+f2muMG5P4VE8T1Li0PdjPUFGjLOO6ahcugKjES8Aqbv12V0ZMlAGLLlN09W5uXTLpLT/7Uzy0e3MZeDQIDWWgKn9AqUVxmxsO4uBW4ApPAKxnJB74M31Zg6','2023-10-04 13:46:34','','');
INSERT INTO otpQ VALUES ('57','254725887269','v+VqujzOedrDcALPYl+eMp+CMgsWEdpG04lgafLuvY4qEkCb48ZPCVNN8C2d803SG4TYARTX6NtyVyG10V2sn0gUjx7Ba+BQA567qmNV4qgZd8yQdEEJEGrXHvU8nBsM','2023-10-04 18:19:01','','');
INSERT INTO otpQ VALUES ('58','254725887269','BSX5h/Vp4aDtw48+1mx5zRBZSAujyai1feXqgT/FKBIa2uy19XcLsiN8koK2Nuk91IZHkDEYMMMZ8IJz1KfaZnu1oPyGbvg61bHsSBhcLj8WrEhsvZADJJ0SIoPg3R0/','2023-10-04 18:20:49','','');
INSERT INTO otpQ VALUES ('59','254725887269','8AEcdSAwwP7mvDQpz2gcjguSD5PeZFRJlk3n66morIhLhc2DraIQRmttNVeKZ1QTbqEddVKQje29O5lBz9XVCztTNolu8DkBbJnx3AtiLpEbqqLlX0t9gXcuEzRock8+','2023-10-04 19:55:09','','');
INSERT INTO otpQ VALUES ('60','254725887269','tiyeMrPbfKLPayCsH/1QKZZeXYlaFYj4rlnnb4exA+Kr1hDwQA3UULaYZUDGpOzGfvAw0ysd+riHRH+ECinvjdeiBs1lv6hhwJMGCjFSsxQSvqC+VTh9151cbHGqqejC','2023-10-04 20:09:28','','');
INSERT INTO otpQ VALUES ('61','254725887269','5Qev6e5IR5k5ptuD6oAuFMyptyFMZmrSKVmCaOLiwERr/RByMDgamX+0bRGMTp5FiD8ZQJ2jC+SsyMcN6iQTzJ2oYq//pRe8dvGWyPsy6OHTMiAAptQYAY62I2i6EhwY','2023-10-04 20:10:47','','');
INSERT INTO otpQ VALUES ('62','254725887269','CfOQSv7Bw5VNocah+9W3QEg3VmMrVNFr2yjqrqsGuO/9WFZZ7rrgVzAjnHYXHDiFDbQ2hcJRMJ6H7cm7cjcb69ChjaHSWZcRbXJxG51zajBue8jaZG9BbX//MxniTHit','2023-10-04 20:12:06','','');
INSERT INTO otpQ VALUES ('63','254725887269','vX8Tl7o2nDR08KJIeqVAL7s2QSGRezau3cIn22HpWGrpevk/4GP8THnV8mn5hUJDy3vYixALfVlwgH5VHxKTMzHA6ZH44EqrSnRkbBd73J4fO1jHbPJe6wS5cRYpDwGw','2023-10-04 20:24:21','','');
INSERT INTO otpQ VALUES ('64','254725887269','9JInFJdQbdFYv/YG+pz0EuZPy99b1R7DRpb006VTKY5siX9lQMcJGOnYmoJVcKn0F/Pzpswu+J/xzN5bSimebHMM+uJ1/OCjpaEIyr1NsHkfWsXhDjKN5y5TcReJz12f','2023-10-04 20:25:25','','');
INSERT INTO otpQ VALUES ('65','254725887269','90eINNDVAw8gQrTuG5EL+Q8hT2ZX7iy4Mml4vi6LzrHPVBBPVF2F3PqAVGe0UiN3cHxQRyH4C24BiWJ3u9BmgLoWFe4WajmLuvmloAz/tUEkk8kqzctCHACKaaEaLlDJ','2023-10-05 12:02:23','','');
INSERT INTO otpQ VALUES ('66','','v953YE8MRTZveShIQJ7NA/h8ONDc7lmxjTnSwmjUtxRDutwvIzRJ2C8ZHq2dI1gH5rUergsbOYUnAl8n+WGQZtcyqnNxQW5Llmpr7OyfOUicPp3Qzriz4zKcZN1rf91c','2023-10-05 13:06:54','','');
INSERT INTO otpQ VALUES ('67','254725887269','JaYqmoqHcUOh6AdC0XSx+iBhCyUY3djIgYJVKhIff3epWXysbfYdjKFXY2+W7vPUKoQ4UX+rqLGYook8NaE4sHKYlFczV0xi317YAz87fGV3Mv6HFEB67R29e1Kj38Ex','2023-10-06 16:11:50','','');
INSERT INTO otpQ VALUES ('68','254725887269','qArT9/YWN3znJBkdH2XOGJshCdjdHgXbSwHX5/Q2iD2wtHDc4I3naM6WPqsU7+9Ji7VK2Uhuc+JcNMdYFc+RmGey15eYXhfkxltbJDUXD3eegjrhdNurtBiPn/reV6Gc','2023-10-06 16:11:54','','');
DROP TABLE IF EXISTS payments;
CREATE TABLE `payments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `services` text NOT NULL,
  `amount` int(11) NOT NULL,
  `staff_name` text NOT NULL,
  `staff_phone` text NOT NULL,
  `date` text NOT NULL,
  `commission_paid` text NOT NULL DEFAULT 'Not Paid',
  `payment_mode` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO payments VALUES ('1','Hasedi nyambura','0700088***','Tips','7500','Dante Koros','0700166***','2023-08-10 17:24:28','Paid','Mpesa Online');
INSERT INTO payments VALUES ('2','Melita vumba','0700166***','Overlay','2000','Dante Koros','0700166***','2023-08-10 18:15:34','Paid','KCB Paybill');
INSERT INTO payments VALUES ('3','Patrice','0798672***','Overlay and gel','4500','Dante Koros','0700166***','2023-08-15 12:28:33','Paid','KCB Paybill');
INSERT INTO payments VALUES ('4','Jennifer nyagate','0725230***','Overlay and gel','5500','Dante Koros','0700166***','2023-08-09 18:56:46','Paid','Mpesa Online');
INSERT INTO payments VALUES ('5','Dorite','0798611***','Stickons ','5000','Dante Koros','0700166***','2023-08-01 18:58:51','Paid','Mpesa Online');
INSERT INTO payments VALUES ('6','Brigit','0763571***','French tips','5500','Dante Koros','0700166***','2023-08-10 20:33:45','Not Paid','Mpesa Online');
INSERT INTO payments VALUES ('7','Lizzie','0724770***','Pedi Gel','3000','Dante Koros','0700166***','2023-07-12 20:43:27','Not Paid','KCB Paybill');
INSERT INTO payments VALUES ('8','Eunice ndugu','0767268***','Pedi gel','3000','Dante Koros','0700166***','2023-07-13 20:04:26','Not Paid','Mpesa Online');
INSERT INTO payments VALUES ('9','Martha ngulio','0716904***',' Pedi gel','30000','Dante Koros','0700166***','2023-07-13 20:05:58','Not Paid','Mpesa Online');
INSERT INTO payments VALUES ('10','Raiyu njaramba','0712339***','Eyebrows and overlay','6000','Dante Koros','0700166***','2023-07-23 20:08:19','Not Paid','Mpesa Online');
INSERT INTO payments VALUES ('11','Wazeri','0790542***','Eyebrows shaping and overlay','7500','Dante Koros','0700166***','2023-07-03 20:10:33','Not Paid','Mpesa Online');
INSERT INTO payments VALUES ('12','Tammy','0725887269','gel','30','demo','254725887269','2023-07-10 16:43:07','Paid','KCB Paybill');
INSERT INTO payments VALUES ('13','Sylvester karungi','00785777','Booking for Bridal makeup','2500','LFH Booking','0787654***','2023-08-17 09:33:19','Not Paid','Mpesa Online');
INSERT INTO payments VALUES ('14','Tim','0725882769','gel','300','demo','254725887269','2023-11-03 10:49:25','Not Paid','KCB Paybill');
DROP TABLE IF EXISTS payments2;
CREATE TABLE `payments2` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `services` text NOT NULL,
  `amount` int(11) NOT NULL,
  `staff_name` text NOT NULL,
  `staff_phone` text NOT NULL,
  `date` text NOT NULL,
  `commission_paid` text NOT NULL DEFAULT 'Not Paid',
  `payment_mode` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
DROP TABLE IF EXISTS performance;
CREATE TABLE `performance` (
  `cashIn` int(11) NOT NULL,
  `cashOut` int(11) NOT NULL,
  `income` int(11) NOT NULL,
  `percent` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO performance VALUES ('82030','48550','33480','40.814336218457%','2023-10-09 16:48:12');
DROP TABLE IF EXISTS performanceHistory;
CREATE TABLE `performanceHistory` (
  `cashIn` int(11) NOT NULL,
  `cashOut` int(11) NOT NULL,
  `income` int(11) NOT NULL,
  `percent` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO performanceHistory VALUES ('24800','1000','23800','95.967741935484%','2023-05-27 17:49:27');
INSERT INTO performanceHistory VALUES ('24800','1000','23800','95.967741935484%','2023-05-27 17:55:29');
INSERT INTO performanceHistory VALUES ('24800','1000','23800','95.967741935484%','2023-05-27 17:56:02');
INSERT INTO performanceHistory VALUES ('24800','3460','21340','86.048387096774%','2023-05-27 18:06:17');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-05-27 18:20:14');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-04 20:03:58');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-04 21:18:39');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:08:35');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:09:45');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:09:49');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:10:03');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:10:05');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:10:30');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:11:27');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:12:58');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:24:44');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:24:49');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:24:53');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:24:59');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:25:03');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:25:38');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:25:45');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:25:48');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:37:21');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:38:30');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:38:39');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:44:18');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:44:26');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:45:50');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:46:01');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:46:02');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:46:02');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:46:03');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:46:03');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:46:04');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:46:04');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:46:04');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:50:48');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:50:53');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:50:56');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:51:24');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:51:36');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:51:45');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:51:48');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:55:33');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 11:58:43');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:09:44');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:11:21');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:13:44');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:14:09');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:16:46');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:17:53');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:21:37');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:21:56');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:21:58');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:21:59');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:22:10');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:22:16');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:41:44');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:43:02');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:43:20');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:50:43');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:55:04');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:56:44');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:58:33');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 13:58:50');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:01:34');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:02:32');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:06:49');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:07:09');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:09:08');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:09:47');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:10:09');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:10:12');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:13:16');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:15:30');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:15:50');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:16:12');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:17:33');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:18:36');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:20:32');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:21:00');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:21:03');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:27:58');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:30:50');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:31:25');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:31:44');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:33:02');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:33:02');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:33:37');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:33:58');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:34:26');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:35:19');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:36:24');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:37:10');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:37:25');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:37:44');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:38:21');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:47:02');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:47:59');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:48:26');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:49:18');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:53:28');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 14:53:54');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 16:36:33');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 18:50:01');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 19:16:11');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 19:45:19');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 20:19:45');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-05 21:59:21');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-07 16:56:03');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-09 15:30:49');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-09 15:31:32');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-12 12:00:46');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-12 16:46:50');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-12 19:03:20');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-14 16:28:01');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-14 16:28:44');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-14 16:29:26');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-14 16:30:54');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-14 16:31:34');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-14 16:33:00');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-14 16:33:40');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-14 16:34:03');
INSERT INTO performanceHistory VALUES ('24800','7780','17020','68.629032258065%','2023-06-14 18:11:35');
INSERT INTO performanceHistory VALUES ('25550','7780','17770','69.549902152642%','2023-06-15 00:57:27');
INSERT INTO performanceHistory VALUES ('25550','7780','17770','69.549902152642%','2023-06-15 00:57:28');
INSERT INTO performanceHistory VALUES ('25550','7780','17770','69.549902152642%','2023-06-15 00:59:03');
INSERT INTO performanceHistory VALUES ('25550','7780','17770','69.549902152642%','2023-06-15 00:59:14');
INSERT INTO performanceHistory VALUES ('25550','7780','17770','69.549902152642%','2023-06-15 00:59:46');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 09:43:58');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 09:45:29');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 09:46:21');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 09:46:41');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 10:27:02');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 10:27:15');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 10:29:44');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 15:05:03');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 15:07:13');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 15:07:19');
INSERT INTO performanceHistory VALUES ('25850','7780','18070','69.903288201161%','2023-06-16 15:09:19');
INSERT INTO performanceHistory VALUES ('26050','7780','18270','70.134357005758%','2023-06-16 21:40:27');
INSERT INTO performanceHistory VALUES ('26050','7780','18270','70.134357005758%','2023-06-16 22:01:46');
INSERT INTO performanceHistory VALUES ('25910','7801','18109','69.891933616364%','2023-06-16 22:35:55');
INSERT INTO performanceHistory VALUES ('26952','8945','18007','66.811368358563%','2023-06-17 22:41:23');
INSERT INTO performanceHistory VALUES ('26750','8945','17805','66.560747663551%','2023-06-20 13:16:26');
INSERT INTO performanceHistory VALUES ('27750','9069','18681','67.318918918919%','2023-06-22 22:43:37');
INSERT INTO performanceHistory VALUES ('28050','16323','11727','41.807486631016%','2023-06-24 15:59:35');
INSERT INTO performanceHistory VALUES ('31600','17351','14249','45.091772151899%','2023-06-29 15:22:37');
INSERT INTO performanceHistory VALUES ('33400','17351','16049','48.050898203593%','2023-07-01 19:03:24');
INSERT INTO performanceHistory VALUES ('33400','17351','16049','48.050898203593%','2023-07-01 19:05:28');
INSERT INTO performanceHistory VALUES ('33400','17401','15999','47.90119760479%','2023-07-01 19:30:30');
INSERT INTO performanceHistory VALUES ('33400','17401','15999','47.90119760479%','2023-07-01 19:31:21');
INSERT INTO performanceHistory VALUES ('33400','17401','15999','47.90119760479%','2023-07-01 19:32:27');
INSERT INTO performanceHistory VALUES ('34650','20177','14473','41.76911976912%','2023-07-04 20:36:41');
INSERT INTO performanceHistory VALUES ('35250','22425','12825','36.382978723404%','2023-07-06 15:57:37');
INSERT INTO performanceHistory VALUES ('35500','22425','13075','36.830985915493%','2023-07-07 08:51:02');
INSERT INTO performanceHistory VALUES ('35500','22425','13075','36.830985915493%','2023-07-07 08:52:28');
INSERT INTO performanceHistory VALUES ('35500','22425','13075','36.830985915493%','2023-07-07 08:55:01');
INSERT INTO performanceHistory VALUES ('36400','20185','16215','44.546703296703%','2023-07-08 20:13:23');
INSERT INTO performanceHistory VALUES ('36400','20185','16215','44.546703296703%','2023-07-09 12:23:52');
INSERT INTO performanceHistory VALUES ('36400','20185','16215','44.546703296703%','2023-07-09 12:26:15');
INSERT INTO performanceHistory VALUES ('36400','20185','16215','44.546703296703%','2023-07-09 12:28:40');
INSERT INTO performanceHistory VALUES ('36400','20185','16215','44.546703296703%','2023-07-09 12:31:10');
INSERT INTO performanceHistory VALUES ('36400','20185','16215','44.546703296703%','2023-07-11 21:32:48');
INSERT INTO performanceHistory VALUES ('37750','20185','17565','46.529801324503%','2023-07-13 13:39:54');
INSERT INTO performanceHistory VALUES ('38500','20315','18185','47.233766233766%','2023-07-15 10:14:22');
INSERT INTO performanceHistory VALUES ('40300','20845','19455','48.275434243176%','2023-07-15 22:23:46');
INSERT INTO performanceHistory VALUES ('40300','20845','19455','48.275434243176%','2023-07-15 22:32:21');
INSERT INTO performanceHistory VALUES ('40300','20845','19455','48.275434243176%','2023-07-16 00:10:11');
INSERT INTO performanceHistory VALUES ('43650','20856','22794','52.219931271478%','2023-07-18 00:45:31');
INSERT INTO performanceHistory VALUES ('43650','20856','22794','52.219931271478%','2023-07-18 00:50:04');
INSERT INTO performanceHistory VALUES ('43650','20856','22794','52.219931271478%','2023-07-18 11:11:13');
INSERT INTO performanceHistory VALUES ('43950','21886','22064','50.202502844141%','2023-07-19 16:57:21');
INSERT INTO performanceHistory VALUES ('45500','22626','22874','50.272527472527%','2023-07-22 10:32:06');
INSERT INTO performanceHistory VALUES ('45500','22626','22874','50.272527472527%','2023-07-22 10:44:18');
INSERT INTO performanceHistory VALUES ('45620','22877','22743','49.853134590092%','2023-07-22 18:01:23');
INSERT INTO performanceHistory VALUES ('45620','24458','21162','46.387549320473%','2023-07-24 13:23:13');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 13:23:48');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 13:51:22');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 13:58:19');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:00:05');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:01:59');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:03:08');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:03:27');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:04:44');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:06:18');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:07:42');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:08:04');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:08:36');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:10:16');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:12:08');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:12:40');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:16:14');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:21:03');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:23:03');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:25:51');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:26:59');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:33:02');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:39:59');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:40:22');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:41:32');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:49:01');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:49:33');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:49:35');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:49:44');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:52:14');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:57:39');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:58:45');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 14:58:54');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 15:05:41');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 15:05:46');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 15:06:08');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 15:06:40');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 15:07:49');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 15:59:48');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 16:06:40');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 16:06:59');
INSERT INTO performanceHistory VALUES ('45590','24458','21132','46.352270234701%','2023-07-24 16:10:26');
INSERT INTO performanceHistory VALUES ('45590','24488','21102','46.286466330336%','2023-07-24 16:33:04');
INSERT INTO performanceHistory VALUES ('45590','24366','21224','46.554068874753%','2023-07-24 17:50:11');
INSERT INTO performanceHistory VALUES ('45590','24366','21224','46.554068874753%','2023-07-24 22:29:25');
INSERT INTO performanceHistory VALUES ('46590','24366','22224','47.701223438506%','2023-07-27 09:45:16');
INSERT INTO performanceHistory VALUES ('46590','24366','22224','47.701223438506%','2023-07-27 09:46:00');
INSERT INTO performanceHistory VALUES ('46590','24366','22224','47.701223438506%','2023-07-27 10:04:48');
INSERT INTO performanceHistory VALUES ('46590','24366','22224','47.701223438506%','2023-07-27 10:05:40');
INSERT INTO performanceHistory VALUES ('46590','24366','22224','47.701223438506%','2023-07-27 11:08:49');
INSERT INTO performanceHistory VALUES ('47290','24366','22924','48.475364770565%','2023-07-28 11:51:21');
INSERT INTO performanceHistory VALUES ('48590','24366','24224','49.853879399053%','2023-07-28 17:44:25');
INSERT INTO performanceHistory VALUES ('49390','24366','25024','50.666126746305%','2023-07-28 21:48:39');
INSERT INTO performanceHistory VALUES ('49390','24366','25024','50.666126746305%','2023-07-29 12:11:42');
INSERT INTO performanceHistory VALUES ('50390','24946','25444','50.494145663822%','2023-07-29 16:18:05');
INSERT INTO performanceHistory VALUES ('50390','24946','25444','50.494145663822%','2023-07-29 16:20:40');
INSERT INTO performanceHistory VALUES ('50390','24946','25444','50.494145663822%','2023-07-29 18:14:48');
INSERT INTO performanceHistory VALUES ('50890','24946','25944','50.980546276282%','2023-07-29 19:44:36');
INSERT INTO performanceHistory VALUES ('50890','24948','25942','50.976616231087%','2023-07-29 20:55:30');
INSERT INTO performanceHistory VALUES ('50890','24948','25942','50.976616231087%','2023-07-29 21:01:20');
INSERT INTO performanceHistory VALUES ('51140','25268','25872','50.590535784122%','2023-07-30 15:18:19');
INSERT INTO performanceHistory VALUES ('51140','25268','25872','50.590535784122%','2023-07-30 19:09:43');
INSERT INTO performanceHistory VALUES ('51140','25268','25872','50.590535784122%','2023-07-31 17:44:58');
INSERT INTO performanceHistory VALUES ('51990','25268','26722','51.398345835738%','2023-08-01 14:05:35');
INSERT INTO performanceHistory VALUES ('52340','25268','27072','51.723347344287%','2023-08-01 17:26:07');
INSERT INTO performanceHistory VALUES ('52340','25268','27072','51.723347344287%','2023-08-01 17:38:37');
INSERT INTO performanceHistory VALUES ('52340','25268','27072','51.723347344287%','2023-08-02 00:27:47');
INSERT INTO performanceHistory VALUES ('54240','28373','25867','47.689896755162%','2023-08-04 23:58:47');
INSERT INTO performanceHistory VALUES ('56000','28573','27427','48.976785714286%','2023-08-05 18:39:03');
INSERT INTO performanceHistory VALUES ('56000','29083','26917','48.066071428571%','2023-08-05 20:01:46');
INSERT INTO performanceHistory VALUES ('57300','32494','24806','43.291448516579%','2023-08-05 23:21:42');
INSERT INTO performanceHistory VALUES ('61550','32644','28906','46.963444354184%','2023-08-08 13:05:40');
INSERT INTO performanceHistory VALUES ('61550','32644','28906','46.963444354184%','2023-08-08 14:50:46');
INSERT INTO performanceHistory VALUES ('63450','33594','29856','47.054373522459%','2023-08-09 17:06:48');
INSERT INTO performanceHistory VALUES ('61950','33594','28356','45.772397094431%','2023-08-09 17:07:57');
INSERT INTO performanceHistory VALUES ('61950','33594','28356','45.772397094431%','2023-08-09 17:09:25');
INSERT INTO performanceHistory VALUES ('62950','33594','29356','46.633836378078%','2023-08-09 22:28:59');
INSERT INTO performanceHistory VALUES ('65750','33594','32156','48.906463878327%','2023-08-10 16:34:14');
INSERT INTO performanceHistory VALUES ('66500','33594','32906','49.482706766917%','2023-08-10 18:13:20');
INSERT INTO performanceHistory VALUES ('68200','38544','29656','43.483870967742%','2023-08-12 05:14:33');
INSERT INTO performanceHistory VALUES ('68200','38544','29656','43.483870967742%','2023-08-12 05:22:14');
INSERT INTO performanceHistory VALUES ('69050','39944','29106','42.152063721941%','2023-08-12 20:43:51');
INSERT INTO performanceHistory VALUES ('5250','9643','-4393','-83.67619047619%','2023-08-14 13:22:32');
INSERT INTO performanceHistory VALUES ('5250','9643','-4393','-83.67619047619%','2023-08-14 13:25:17');
INSERT INTO performanceHistory VALUES ('5250','9643','-4393','-83.67619047619%','2023-08-14 13:26:54');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-14 22:09:19');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 08:16:47');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 08:16:53');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 08:18:46');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 09:57:29');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:25:13');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:27:25');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:28:45');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:29:13');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:29:32');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:31:23');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:32:25');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:33:16');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:33:19');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:33:22');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:33:50');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:34:59');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:35:03');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:35:09');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:35:12');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:36:12');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:36:22');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:36:44');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:38:03');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:38:19');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:40:49');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:41:14');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:41:17');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:41:35');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 10:55:54');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 14:15:10');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 14:27:29');
INSERT INTO performanceHistory VALUES ('5550','9643','-4093','-73.747747747748%','2023-08-16 15:04:27');
INSERT INTO performanceHistory VALUES ('5550','96430','-90880','-1637.4774774775%','2023-08-16 18:57:10');
INSERT INTO performanceHistory VALUES ('5550','96430','-90880','-1637.4774774775%','2023-08-16 20:32:20');
INSERT INTO performanceHistory VALUES ('55500','96430','-40930','-73.747747747748%','2023-08-16 20:35:48');
INSERT INTO performanceHistory VALUES ('55500','96430','-40930','-73.747747747748%','2023-08-16 20:37:53');
INSERT INTO performanceHistory VALUES ('55500','96430','-40930','-73.747747747748%','2023-08-16 20:38:58');
INSERT INTO performanceHistory VALUES ('55500','96430','-40930','-73.747747747748%','2023-08-16 20:40:59');
INSERT INTO performanceHistory VALUES ('55500','96430','-40930','-73.747747747748%','2023-08-16 20:42:19');
INSERT INTO performanceHistory VALUES ('60500','96430','-35930','-59.388429752066%','2023-08-17 17:12:36');
INSERT INTO performanceHistory VALUES ('58000','96458','-38458','-66.306896551724%','2023-09-20 21:58:06');
INSERT INTO performanceHistory VALUES ('58000','96458','-38458','-66.306896551724%','2023-09-20 21:58:50');
INSERT INTO performanceHistory VALUES ('58000','96458','-38458','-66.306896551724%','2023-09-20 21:58:57');
INSERT INTO performanceHistory VALUES ('58000','96458','-38458','-66.306896551724%','2023-09-20 21:59:03');
INSERT INTO performanceHistory VALUES ('58000','96458','-38458','-66.306896551724%','2023-09-20 21:59:16');
INSERT INTO performanceHistory VALUES ('55030','96469','-41439','-75.302562238779%','2023-09-29 12:00:55');
INSERT INTO performanceHistory VALUES ('58000','96469','-38469','-66.325862068966%','2023-10-03 15:39:12');
INSERT INTO performanceHistory VALUES ('58000','96469','-38469','-66.325862068966%','2023-10-03 15:40:37');
INSERT INTO performanceHistory VALUES ('58000','96469','-38469','-66.325862068966%','2023-10-03 15:49:38');
INSERT INTO performanceHistory VALUES ('55030','96523','-41493','-75.400690532437%','2023-10-07 11:52:06');
INSERT INTO performanceHistory VALUES ('55030','96523','-41493','-75.400690532437%','2023-10-07 11:53:03');
INSERT INTO performanceHistory VALUES ('55030','96523','-41493','-75.400690532437%','2023-10-07 12:00:02');
INSERT INTO performanceHistory VALUES ('55030','96523','-41493','-75.400690532437%','2023-10-09 15:54:00');
INSERT INTO performanceHistory VALUES ('55030','96523','-41493','-75.400690532437%','2023-10-09 15:54:23');
INSERT INTO performanceHistory VALUES ('55030','96523','-41493','-75.400690532437%','2023-10-09 16:31:05');
INSERT INTO performanceHistory VALUES ('82030','96430','-14400','-17.554553212239%','2023-10-09 16:47:27');
INSERT INTO performanceHistory VALUES ('82030','39100','42930','52.334511763989%','2023-10-09 16:48:00');
INSERT INTO performanceHistory VALUES ('82030','48550','33480','40.814336218457%','2023-10-09 16:48:12');
DROP TABLE IF EXISTS recruit;
CREATE TABLE `recruit` (
  `staff_no` int(11) NOT NULL AUTO_INCREMENT,
  `staff_name` text NOT NULL,
  `staff_phone` text NOT NULL,
  `staff_email` text NOT NULL,
  `joinDate` text NOT NULL,
  `skills` text NOT NULL,
  `cv` text DEFAULT NULL,
  PRIMARY KEY (`staff_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
DROP TABLE IF EXISTS recurrentExp;
CREATE TABLE `recurrentExp` (
  `s_no` int(11) NOT NULL,
  `name` text NOT NULL,
  `amount` text NOT NULL,
  `date` text NOT NULL,
  `currentTotal` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO recurrentExp VALUES ('1','Rent','5000','2023-07-24 19:28:21','5000');
INSERT INTO recurrentExp VALUES ('2','Wifi','1000','2023-07-24 19:28:43','6000');
INSERT INTO recurrentExp VALUES ('3','Electricity','500','2023-07-24 19:30:28','6500');
INSERT INTO recurrentExp VALUES ('4','Salary','5000','2023-07-24 19:30:41','11500');
INSERT INTO recurrentExp VALUES ('5','Misc Expenses','3000','2023-07-27 10:04:06','14500');
DROP TABLE IF EXISTS sentSMS;
CREATE TABLE `sentSMS` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` text NOT NULL,
  `message` text NOT NULL,
  `date` text NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO sentSMS VALUES ('1','+254725887269','Hello Lourice, thank you for your interest in our services. Use code: a3ebec for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-22 13:47:04');
INSERT INTO sentSMS VALUES ('2','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: abe7e5 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-22 13:50:14');
INSERT INTO sentSMS VALUES ('3','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: aebd2e for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-22 13:53:51');
INSERT INTO sentSMS VALUES ('4','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 041386 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-22 13:56:35');
INSERT INTO sentSMS VALUES ('5','+254725882769','Dear Tim, thank you for visiting EXCEL TECH ESSENTIALS. Payment received: Kshs.30000. Loyalty Points now at 300. See you again. www.essentialtech.site','2023-08-22 17:27:34');
INSERT INTO sentSMS VALUES ('6','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 608472 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-22 14:33:31');
INSERT INTO sentSMS VALUES ('7','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 683914 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-22 14:38:10');
INSERT INTO sentSMS VALUES ('8','254725887269','sds
Lourice Beauty Parlour. www.lfhcompany.site','2023-08-22 15:00:42');
INSERT INTO sentSMS VALUES ('9','+254725882769','Dear Tim, thank you for visiting EXCEL TECH ESSENTIALS. Payment received: Kshs.30000. Loyalty Points now at 300. See you again. www.essentialtech.site','2023-08-22 18:32:48');
INSERT INTO sentSMS VALUES ('10','+254718509240','Hello Sweet savour perfumes , thank you for your interest in our services. Use code: 574892 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-22 16:34:02');
INSERT INTO sentSMS VALUES ('11','+254254','Hello Cute, thank you for your interest in our services. Use code: 637289 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-24 13:08:27');
INSERT INTO sentSMS VALUES ('12','+254710422071','Hello Oneclin , thank you for your interest in our services. Use code: 213480 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-24 16:20:05');
INSERT INTO sentSMS VALUES ('13','+254733440443','Hello Fashion, thank you for your interest in our services. Use code: 173650 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-26 11:33:43');
INSERT INTO sentSMS VALUES ('14','+254725887269','Hello Smart, thank you for your interest in our services. Use code: 435710 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-08-28 16:53:42');
INSERT INTO sentSMS VALUES ('15','','','2023-08-29 12:30:39');
INSERT INTO sentSMS VALUES ('16','','','2023-08-29 12:32:03');
INSERT INTO sentSMS VALUES ('17','','','2023-08-29 12:35:56');
INSERT INTO sentSMS VALUES ('18','','','2023-08-29 12:37:40');
INSERT INTO sentSMS VALUES ('19','','','2023-08-29 12:37:45');
INSERT INTO sentSMS VALUES ('20','','','2023-08-29 12:37:48');
INSERT INTO sentSMS VALUES ('21','','','2023-08-29 12:37:49');
INSERT INTO sentSMS VALUES ('22','','','2023-08-29 12:37:49');
INSERT INTO sentSMS VALUES ('23','','','2023-08-29 12:37:50');
INSERT INTO sentSMS VALUES ('24','','','2023-08-29 12:37:51');
INSERT INTO sentSMS VALUES ('25','','','2023-08-29 12:37:52');
INSERT INTO sentSMS VALUES ('26','','','2023-08-29 12:37:52');
INSERT INTO sentSMS VALUES ('27','','','2023-08-29 12:37:53');
INSERT INTO sentSMS VALUES ('28','','','2023-08-29 12:37:54');
INSERT INTO sentSMS VALUES ('29','','','2023-08-29 12:37:55');
INSERT INTO sentSMS VALUES ('30','','','2023-08-29 12:37:55');
INSERT INTO sentSMS VALUES ('31','','','2023-08-29 12:37:56');
INSERT INTO sentSMS VALUES ('32','','','2023-08-29 12:37:57');
INSERT INTO sentSMS VALUES ('33','','','2023-08-29 12:37:58');
INSERT INTO sentSMS VALUES ('34','','','2023-08-29 12:47:54');
INSERT INTO sentSMS VALUES ('35','254725887269','Testing','2023-08-30 11:30:36');
INSERT INTO sentSMS VALUES ('36','254725887269','Testing','2023-08-30 11:58:07');
INSERT INTO sentSMS VALUES ('37','254725887269','Testing1','2023-08-30 12:01:15');
INSERT INTO sentSMS VALUES ('38','254725887269','Testing','2023-08-30 12:17:12');
INSERT INTO sentSMS VALUES ('39','254725887269','LFH','2023-08-30 12:52:36');
INSERT INTO sentSMS VALUES ('40','254725887269','message','2023-08-30 12:53:15');
INSERT INTO sentSMS VALUES ('41','254725887269','LFH test','2023-08-30 12:54:01');
INSERT INTO sentSMS VALUES ('42','254725887269','LFH test','2023-08-30 12:55:55');
INSERT INTO sentSMS VALUES ('43','254725887269','Postman','2023-08-30 12:59:36');
INSERT INTO sentSMS VALUES ('44','254725887269','LFH testing with Auth','2023-08-30 14:27:08');
INSERT INTO sentSMS VALUES ('45','254725887269','LFH testing with Auth','2023-08-30 14:33:04');
INSERT INTO sentSMS VALUES ('46','254725887269','LFH testing with Auth','2023-08-30 14:35:42');
INSERT INTO sentSMS VALUES ('47','254725887269','LFH testing with Auth and user','2023-08-30 14:35:57');
INSERT INTO sentSMS VALUES ('48','254725887269','LFH testing with Auth & user','2023-08-30 14:37:27');
INSERT INTO sentSMS VALUES ('49','254725887269','LFH with Auth & user','2023-08-30 14:38:58');
INSERT INTO sentSMS VALUES ('50','254725887269','LFH with Auth','2023-08-30 14:39:59');
INSERT INTO sentSMS VALUES ('51','254725887269','LFH with Auth','2023-08-30 14:48:09');
INSERT INTO sentSMS VALUES ('52','254725887269','LFH with Auth','2023-08-30 15:20:35');
INSERT INTO sentSMS VALUES ('53','254725887269','LFH with Auth','2023-08-30 22:15:36');
INSERT INTO sentSMS VALUES ('54','254725887269','LFH with Auth','2023-08-30 22:15:39');
INSERT INTO sentSMS VALUES ('55','254725887269','LFH with Auth','2023-08-30 22:24:05');
INSERT INTO sentSMS VALUES ('56','254725887269','LFH with Auth','2023-08-30 22:25:35');
INSERT INTO sentSMS VALUES ('57','254725887269','LFH with Auth','2023-08-30 22:26:57');
INSERT INTO sentSMS VALUES ('58','254725887269','LFH with Auth','2023-08-30 22:30:25');
INSERT INTO sentSMS VALUES ('59','254725887269','LFH with Auth ttt','2023-08-30 22:30:42');
INSERT INTO sentSMS VALUES ('60','254725887269','LFH with Auth ttt','2023-08-30 22:30:59');
INSERT INTO sentSMS VALUES ('61','254725887269','LFH with Auth ttt','2023-08-30 22:31:46');
INSERT INTO sentSMS VALUES ('62','254725887269','LFH with Auth ttt','2023-08-30 22:32:35');
INSERT INTO sentSMS VALUES ('63','254725887269','LFH with Auth ttt','2023-08-30 22:36:52');
INSERT INTO sentSMS VALUES ('64','254725887269','LFH with Auth ttt','2023-08-30 22:37:54');
INSERT INTO sentSMS VALUES ('65','+254725887269','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','2023-08-30 23:50:56');
INSERT INTO sentSMS VALUES ('66','+254733440443','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','2023-08-31 17:55:11');
INSERT INTO sentSMS VALUES ('67','254725887269','LFH with Auth ttt','2023-08-31 21:59:40');
INSERT INTO sentSMS VALUES ('68','+254725887269','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','2023-09-01 09:28:47');
INSERT INTO sentSMS VALUES ('69','+254733440443','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','2023-09-01 09:30:51');
INSERT INTO sentSMS VALUES ('70','+2540098475','Dear Tim, thank you for visiting EXCEL TECH ESSENTIALS. Payment received: Kshs.20000. Loyalty Points now at 250. See you again. www.essentialtech.site','2023-09-01 20:20:52');
INSERT INTO sentSMS VALUES ('71','254725887269','Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 06-09-2023. Transaction reference number RI652TDHW7. Thank you.','2023-09-06 16:48:03');
INSERT INTO sentSMS VALUES ('72','254725887269','Hi, your payment of Kshs.5.00 to 254725887269 is Unsuccessful on 06-09-2023. Transaction reference number RI682TX3BU. Thank you.','2023-09-06 16:53:02');
INSERT INTO sentSMS VALUES ('73','+254798297323','Hello Cute girl , thank you for your interest in our services. Use code: 247193 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-06 22:46:54');
INSERT INTO sentSMS VALUES ('74','254725887269','Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 06-09-2023. Transaction reference number RI6444AGR8. Thank you.','2023-09-06 23:18:04');
INSERT INTO sentSMS VALUES ('75','254725887269','Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 06-09-2023. Transaction reference number RI6444BOTM. Thank you.','2023-09-06 23:19:04');
INSERT INTO sentSMS VALUES ('76','+254733440443','Hello Lourice, thank you for your interest in our services. Use code: 137829 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-07 16:55:00');
INSERT INTO sentSMS VALUES ('77','254725887269','OTP: 123456','2023-09-08 16:37:42');
INSERT INTO sentSMS VALUES ('78','254725887269','OTP: 123456','2023-09-08 16:38:14');
INSERT INTO sentSMS VALUES ('79','254725887269','OTP: 123456','2023-09-08 16:39:16');
INSERT INTO sentSMS VALUES ('80','254725887269','OTP: 123456','2023-09-08 16:43:32');
INSERT INTO sentSMS VALUES ('81','254725887269','OTP: 123456','2023-09-08 16:50:35');
INSERT INTO sentSMS VALUES ('82','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 82Ro6erB5fqmT3arNh8XkqCS6L7nBLOb6TR5cjZZqRLjE+COExt2CAS9+YVSw0wDzMd+tP2SVqh2SBPd7cl+NpTxpwuBKcB/I98brV2DgpGRD9MldOpFIqGccGqAmmpQ for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 08:56:04');
INSERT INTO sentSMS VALUES ('83','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 632451 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 08:58:44');
INSERT INTO sentSMS VALUES ('84','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 458302 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 08:59:48');
INSERT INTO sentSMS VALUES ('85','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 702491 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 09:04:41');
INSERT INTO sentSMS VALUES ('86','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 097618 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 09:10:09');
INSERT INTO sentSMS VALUES ('87','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 103762 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 09:52:59');
INSERT INTO sentSMS VALUES ('88','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 093716 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 09:55:07');
INSERT INTO sentSMS VALUES ('89','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 809625 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 09:55:21');
INSERT INTO sentSMS VALUES ('90','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 965873 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 09:57:38');
INSERT INTO sentSMS VALUES ('91','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 714528 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 09:58:32');
INSERT INTO sentSMS VALUES ('92','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 402376 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:01:09');
INSERT INTO sentSMS VALUES ('93','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 247638 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:02:52');
INSERT INTO sentSMS VALUES ('94','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 196745 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:04:12');
INSERT INTO sentSMS VALUES ('95','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 047159 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:05:27');
INSERT INTO sentSMS VALUES ('96','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 720894 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:06:17');
INSERT INTO sentSMS VALUES ('97','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 926140 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:11:06');
INSERT INTO sentSMS VALUES ('98','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 650784 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:11:58');
INSERT INTO sentSMS VALUES ('99','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 604958 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:13:23');
INSERT INTO sentSMS VALUES ('100','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 135426 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:17:53');
INSERT INTO sentSMS VALUES ('101','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 023815 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:34:28');
INSERT INTO sentSMS VALUES ('102','+254725887269','Hello Lourice, thank you for your interest in our services. Use code: 679184 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:36:47');
INSERT INTO sentSMS VALUES ('103','+254725887269','Hello Lourice, thank you for your interest in our services. Use code: 491567 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:39:31');
INSERT INTO sentSMS VALUES ('104','+254725887269','Hello Lourice, thank you for your interest in our services. Use code: 598672 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:39:56');
INSERT INTO sentSMS VALUES ('105','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 103726 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:41:55');
INSERT INTO sentSMS VALUES ('106','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 845173 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:44:18');
INSERT INTO sentSMS VALUES ('107','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 482960 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:44:50');
INSERT INTO sentSMS VALUES ('108','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 415296 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-09 10:50:01');
INSERT INTO sentSMS VALUES ('109','+254725887269','Hello Excel Tech, thank you for your interest in our services. Use code: 019384 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-10 13:13:15');
INSERT INTO sentSMS VALUES ('110','+254540099212','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','2023-09-10 18:46:30');
INSERT INTO sentSMS VALUES ('111','+254540099212','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','2023-09-10 18:55:18');
INSERT INTO sentSMS VALUES ('112','+254540099212','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','2023-09-10 19:02:02');
INSERT INTO sentSMS VALUES ('113','+254540099212','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','2023-09-10 19:19:54');
INSERT INTO sentSMS VALUES ('114','+254710422071','Dear Peter wano, thank you for visiting EXCEL TECH ESSENTIALS. Payment received: Kshs.600. Loyalty Points now at 6. See you again. www.essentialtech.site','2023-09-11 02:34:14');
INSERT INTO sentSMS VALUES ('115','254710422071','Hello 
Excel Tech Essentials. www.essentialtech.site','2023-09-10 23:46:15');
INSERT INTO sentSMS VALUES ('116','+254545887269','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','2023-09-11 01:42:59');
INSERT INTO sentSMS VALUES ('117','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 719256 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-11 12:10:00');
INSERT INTO sentSMS VALUES ('118','+254725887269','Hello Excel Tech Essentials, thank you for your interest in our services. Use code: 350764 for each Login to the Demo. Login here www.essentialtech.site/demo','2023-09-11 12:15:31');
INSERT INTO sentSMS VALUES ('119','254725887269','Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS0Z1GFH6. Thank you.','2023-09-28 17:10:05');
INSERT INTO sentSMS VALUES ('120','','Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS1Z246VN. Thank you.','2023-09-28 17:15:03');
INSERT INTO sentSMS VALUES ('121','254725887269','Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS2Z2OA4C. Thank you.','2023-09-28 17:19:06');
INSERT INTO sentSMS VALUES ('122','254725887269','Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 28-09-2023. Transaction reference number RIS9Z9045L. Thank you.','2023-09-28 18:05:02');
INSERT INTO sentSMS VALUES ('123','254725887269','Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 28-09-2023. Transaction reference number RIS7Z9L4T9. Thank you.','2023-09-28 18:09:02');
INSERT INTO sentSMS VALUES ('124','254725887269','Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 28-09-2023. Transaction reference number RIS7ZA5JML. Thank you.','2023-09-28 18:12:04');
INSERT INTO sentSMS VALUES ('125','254725887269','Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 28-09-2023. Transaction reference number RIS3ZBP04N. Thank you.','2023-09-28 18:22:03');
INSERT INTO sentSMS VALUES ('126','254725887269','Hi, your payment of Kshs.10.00 to 254725887269 is Successful on 28-09-2023. Transaction reference number RIS8ZC70H2. Thank you.','2023-09-28 18:26:07');
INSERT INTO sentSMS VALUES ('127','254725887269','Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS5ZCHZ1F. Thank you.','2023-09-28 18:27:05');
INSERT INTO sentSMS VALUES ('128','254725887269','Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS1ZDJPOP. Thank you.','2023-09-28 18:34:04');
INSERT INTO sentSMS VALUES ('129','254725887269','Hi, your payment of Kshs.1.00 to 254725887269 is Unsuccessful on 28-09-2023. Transaction reference number RIS2ZEDVCM. Thank you.','2023-09-28 18:39:02');
INSERT INTO sentSMS VALUES ('130','254725887269','Hi, your payment of Kshs. to  is  on 28-09-2023. Transaction reference number . Thank you.','2023-09-28 18:41:25');
INSERT INTO sentSMS VALUES ('131','254725887269','Hi, your payment of Kshs. to  is  on 28-09-2023. Transaction reference number . Thank you.','2023-09-28 22:19:18');
INSERT INTO sentSMS VALUES ('132','254725887269','Hi, your payment of Kshs. to  is  on 29-09-2023. Transaction reference number . Thank you.','2023-09-29 09:18:53');
INSERT INTO sentSMS VALUES ('133','254725887269','OTP: 658953','2023-10-03 12:05:19');
INSERT INTO sentSMS VALUES ('134','254725887269','OTP: 483448','2023-10-03 12:05:21');
INSERT INTO sentSMS VALUES ('135','254725887269','OTP: 524692','2023-10-03 12:06:26');
INSERT INTO sentSMS VALUES ('136','254725887269','OTP: 516384','2023-10-04 11:54:51');
INSERT INTO sentSMS VALUES ('137','254725887269','OTP: 522802','2023-10-04 11:57:54');
INSERT INTO sentSMS VALUES ('138','254725887269','OTP: 510808','2023-10-04 12:02:14');
INSERT INTO sentSMS VALUES ('139','254725887269','OTP: 316060','2023-10-04 12:05:30');
INSERT INTO sentSMS VALUES ('140','254725887269','OTP: 344856','2023-10-04 13:04:34');
INSERT INTO sentSMS VALUES ('141','254725887269','OTP: 280427','2023-10-04 13:06:21');
INSERT INTO sentSMS VALUES ('142','254725887269','OTP: 580794','2023-10-04 13:35:19');
INSERT INTO sentSMS VALUES ('143','254725887269','OTP: 828373','2023-10-04 13:45:21');
INSERT INTO sentSMS VALUES ('144','254725887269','OTP: 946996','2023-10-04 13:46:34');
INSERT INTO sentSMS VALUES ('145','254725887269','OTP: 761252','2023-10-04 18:19:01');
INSERT INTO sentSMS VALUES ('146','254725887269','OTP: 237850','2023-10-04 18:20:49');
INSERT INTO sentSMS VALUES ('147','254725887269','OTP: 215635','2023-10-04 19:55:09');
INSERT INTO sentSMS VALUES ('148','254725887269','Test','2023-10-04 20:07:34');
INSERT INTO sentSMS VALUES ('149','254725887269','OTP: 767412','2023-10-04 20:09:28');
INSERT INTO sentSMS VALUES ('150','254725887269','Test','2023-10-04 20:10:03');
INSERT INTO sentSMS VALUES ('151','254725887269','Test','2023-10-04 20:10:04');
INSERT INTO sentSMS VALUES ('152','254725887269','OTP: 112412','2023-10-04 20:10:47');
INSERT INTO sentSMS VALUES ('153','254725887269','OTP: 373444','2023-10-04 20:12:06');
INSERT INTO sentSMS VALUES ('154','254725887269','Test','2023-10-04 20:12:27');
INSERT INTO sentSMS VALUES ('155','254725887269','Test','2023-10-04 20:13:59');
INSERT INTO sentSMS VALUES ('156','0725887269','Test','2023-10-04 20:14:19');
INSERT INTO sentSMS VALUES ('157','0725887269','Testing','2023-10-04 20:21:44');
INSERT INTO sentSMS VALUES ('158','254725887269','OTP: 645118','2023-10-04 20:24:21');
INSERT INTO sentSMS VALUES ('159','254725887269','OTP: 939005','2023-10-04 20:25:25');
DROP TABLE IF EXISTS signup;
CREATE TABLE `signup` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `bizname` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `code` text NOT NULL,
  `username` text NOT NULL DEFAULT 'demo',
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO signup VALUES ('1','Excel Tech Essentials','info@essentialtech.site','254725887269','0TYdqr/MswcbtknJ3jYiWIzjbVhU4a/Sv6l4FoTry7y/MImtxZ7dFkZXjJiAdjKC0WOb2zqtVEun+ukidBeZPAWuoHhB+0EFMvUHPMnhgAGWLt8Npo3dQXE0Xjn1ueHn','demo');
INSERT INTO signup VALUES ('2','Sweet savour perfumes ','njorogeoffice@gmail.com','254718509240','88PcA+38JY2QQYJBGkglCE2HIGiemz6n0KL9xX82xwoCGmWtP3ACgf0oEvMe1fbsgB8rhZzvJeaG8hzyVguRxvQFK4U6BnKTtQ9RZl3wdDZ8tXUjaOJbz3+vq8E3iZi4','demo');
INSERT INTO signup VALUES ('3','Oneclin ','peterwano@gmail.com','254710422071','jVWmjvNaVdu9oH0aySSqvEbOKHDHXjXbZUaXGEb8HZRr3DI9VR5stIzu1LDOMVJmvzAZ1zGO3pNXN9uvSyaegrp+lGkSbdSZsiI0WKsFIxoNuuPDJSoq37JP9G0lYx4Q','demo');
INSERT INTO signup VALUES ('4','Fashion','timnmburu1@gmail.com','254733440443','O3q7ahhg6mS4Yl6LfMJkwyqpIYUA0+5IsLi9Zwpj3cabTeZqZLbo+mhnwIGt0zT3VF30ZCs+WBuOc0+2jsXrc24MUhqQjBqsJ+r6K1bhaN8vKuUt1sqgWs98LeUsulkB','demo');
INSERT INTO signup VALUES ('5','Cute girl ','ceciliandindah@gmail.com','254798297323','CZvgYJgkS0IuRTm5RvCLyjIgGJp2cSnHnOypjlOLr+KTcvHsWLmp7bxhIFaqEirgvBpPXIxPMSFKrjU4QxG8CjqP2kr0GO614ITQofcC54O0fxaPbtpPSZoOUbY23VNU','demo');
INSERT INTO signup VALUES ('6','Karugia beauty','sabinamburu22@gmail.com','254104874505','mjE0YTnkvq89scI+mht3y1V5mrRpB5YssmDiXFff+rsRNEMqf7kz0NoiHuKrLHmghpGJA4SasgJrOR5Q2tZEwBUy/AXPaklTwJtyenB3Nqk0GnGoDcZvkfBUIXXfOYvx','demo');
INSERT INTO signup VALUES ('7','Karugia beauty','sabinamburu22@gmail.com','254104874805','nGm5qOzhQvl0EqsLepIJ6ErdciBjkQ9aMyHJqJk7z+1PkRc8n/zfhtwk5GTQhS25Xx2noQtw4SLE2rY0YTnlmkgdfBP5kBU09kxr6z5Nxh0oC3eYxjGIGDr5a/gS+PHj','demo');
INSERT INTO signup VALUES ('8','NC Contractors Ltd ','buildersnc26@gmail.com','254722981937','yKZdeX//+0G+3BDXlZXMkqGUK6/wgsn4IzsDa3uFBfD3a4k3QnSjO7JQ1aMEze16JDmig/CxHMm2nrBZhDaavkPQh3fcFNymujLlIufkstCjwIxUb5Q194PUxK2TlfDA','demo');
INSERT INTO signup VALUES ('9','Tiske Royal Enterprise ','hawomoha8@gmail.com','254717789276','qzEZR4/lzMW3aaUoRpPlmLNjt8wDSLW4RAMVvXjPybvoPwTnAF7lZ6QAOdfP9w+sVSCM8uQ7pV7biLtZIHEHzeHorPnVQ94C62ZXEoxsht/vg6Oyuz+7AsOaEuzYJyIJ','demo');
DROP TABLE IF EXISTS smsQ;
CREATE TABLE `smsQ` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` text NOT NULL,
  `message` text NOT NULL,
  `sender1` text NOT NULL,
  `sender2` text NOT NULL,
  `dateInitiated` text NOT NULL,
  `dateDelivered` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO smsQ VALUES ('40','+254725887269','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=%2B254725887269&message=Dear+Customer%2C+thank+you+for+choosing+us.+Your+booking+has+been+captured+successfully+and+is+being+processed.+Please+wait+for+us+to+contact+you+on+the+next+steps.+www.essentialtech.site&msgtype=5&dlr=0','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Dear+Customer%2C+thank+you+for+choosing+us.+Your+booking+has+been+captured+successfully+and+is+being+processed.+Please+wait+for+us+to+contact+you+on+the+next+steps.+www.essentialtech.site&shortcode=JuaMobile&mobile=%2B254725887269','2023-08-30 23:43:15','','');
INSERT INTO smsQ VALUES ('46','+254733440443','Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=%2B254733440443&message=Dear+Customer%2C+thank+you+for+choosing+us.+Your+booking+has+been+captured+successfully+and+is+being+processed.+Please+wait+for+us+to+contact+you+on+the+next+steps.+www.essentialtech.site&msgtype=5&dlr=0','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Dear+Customer%2C+thank+you+for+choosing+us.+Your+booking+has+been+captured+successfully+and+is+being+processed.+Please+wait+for+us+to+contact+you+on+the+next+steps.+www.essentialtech.site&shortcode=JuaMobile&mobile=%2B254733440443','2023-08-31 17:55:11','','');
INSERT INTO smsQ VALUES ('47','254725887269','LFH with Auth ttt','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=LFH+with+Auth+ttt&msgtype=5&dlr=0','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=LFH+with+Auth+ttt&shortcode=JuaMobile&mobile=254725887269','2023-08-31 21:59:40','','');
INSERT INTO smsQ VALUES ('48','254725887269','OTP: 658953','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+658953&messageID=OTP%3A+658953&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+658953&msgtype=5&dlr=0','2023-10-03 12:05:19','','');
INSERT INTO smsQ VALUES ('49','254725887269','OTP: 483448','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+483448&messageID=OTP%3A+483448&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+483448&msgtype=5&dlr=0','2023-10-03 12:05:21','','');
INSERT INTO smsQ VALUES ('50','254725887269','OTP: 524692','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+524692&messageID=OTP%3A+524692&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+524692&msgtype=5&dlr=0','2023-10-03 12:06:26','','');
INSERT INTO smsQ VALUES ('51','254725887269','OTP: 516384','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+516384&messageID=OTP%3A+516384&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+516384&msgtype=5&dlr=0','2023-10-04 11:54:51','','');
INSERT INTO smsQ VALUES ('52','254725887269','OTP: 522802','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+522802&messageID=OTP%3A+522802&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+522802&msgtype=5&dlr=0','2023-10-04 11:57:54','','');
INSERT INTO smsQ VALUES ('53','254725887269','OTP: 510808','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+510808&messageID=OTP%3A+510808&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+510808&msgtype=5&dlr=0','2023-10-04 12:02:14','','');
INSERT INTO smsQ VALUES ('54','254725887269','OTP: 316060','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+316060&messageID=OTP%3A+316060&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+316060&msgtype=5&dlr=0','2023-10-04 12:05:30','','');
INSERT INTO smsQ VALUES ('55','254725887269','OTP: 344856','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+344856&messageID=OTP%3A+344856&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+344856&msgtype=5&dlr=0','2023-10-04 13:04:34','','');
INSERT INTO smsQ VALUES ('56','254725887269','OTP: 280427','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+280427&messageID=OTP%3A+280427&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+280427&msgtype=5&dlr=0','2023-10-04 13:06:21','','');
INSERT INTO smsQ VALUES ('57','254725887269','OTP: 580794','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+580794&messageID=OTP%3A+580794&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+580794&msgtype=5&dlr=0','2023-10-04 13:35:19','','');
INSERT INTO smsQ VALUES ('58','254725887269','OTP: 828373','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+828373&messageID=OTP%3A+828373&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+828373&msgtype=5&dlr=0','2023-10-04 13:45:21','','');
INSERT INTO smsQ VALUES ('59','254725887269','OTP: 946996','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+946996&messageID=OTP%3A+946996&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+946996&msgtype=5&dlr=0','2023-10-04 13:46:34','','');
INSERT INTO smsQ VALUES ('60','254725887269','OTP: 761252','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+761252&messageID=OTP%3A+761252&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+761252&msgtype=5&dlr=0','2023-10-04 18:19:01','','');
INSERT INTO smsQ VALUES ('61','254725887269','OTP: 237850','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+237850&messageID=OTP%3A+237850&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+237850&msgtype=5&dlr=0','2023-10-04 18:20:49','','');
INSERT INTO smsQ VALUES ('62','254725887269','OTP: 215635','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+215635&messageID=OTP%3A+215635&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+215635&msgtype=5&dlr=0','2023-10-04 19:55:09','','');
INSERT INTO smsQ VALUES ('63','254725887269','Test','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Test&messageID=Test&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=Test&msgtype=5&dlr=0','2023-10-04 20:07:34','','');
INSERT INTO smsQ VALUES ('64','254725887269','OTP: 767412','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+767412&messageID=OTP%3A+767412&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+767412&msgtype=5&dlr=0','2023-10-04 20:09:28','','');
INSERT INTO smsQ VALUES ('65','254725887269','Test','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Test&messageID=Test&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=Test&msgtype=5&dlr=0','2023-10-04 20:10:03','','');
INSERT INTO smsQ VALUES ('66','254725887269','Test','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Test&messageID=Test&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=Test&msgtype=5&dlr=0','2023-10-04 20:10:04','','');
INSERT INTO smsQ VALUES ('67','254725887269','OTP: 112412','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+112412&messageID=OTP%3A+112412&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+112412&msgtype=5&dlr=0','2023-10-04 20:10:47','','');
INSERT INTO smsQ VALUES ('68','254725887269','OTP: 373444','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+373444&messageID=OTP%3A+373444&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+373444&msgtype=5&dlr=0','2023-10-04 20:12:06','','');
INSERT INTO smsQ VALUES ('69','254725887269','Test','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Test&messageID=Test&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=Test&msgtype=5&dlr=0','2023-10-04 20:12:27','','');
INSERT INTO smsQ VALUES ('70','254725887269','Test','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Test&messageID=Test&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=Test&msgtype=5&dlr=0','2023-10-04 20:13:59','','');
INSERT INTO smsQ VALUES ('71','0725887269','Test','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Test&messageID=Test&shortcode=JuaMobile&mobile=0725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=0725887269&message=Test&msgtype=5&dlr=0','2023-10-04 20:14:19','','');
INSERT INTO smsQ VALUES ('72','0725887269','Testing','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=Testing&messageID=Testing&shortcode=JuaMobile&mobile=0725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=0725887269&message=Testing&msgtype=5&dlr=0','2023-10-04 20:21:44','','');
INSERT INTO smsQ VALUES ('73','254725887269','OTP: 645118','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+645118&messageID=OTP%3A+645118&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+645118&msgtype=5&dlr=0','2023-10-04 20:24:21','','');
INSERT INTO smsQ VALUES ('74','254725887269','OTP: 939005','https://quicksms.advantasms.com/api/services/sendsms/?&apikey=5d4f4e80a25b3939e828d534e6ca3117&partnerID=7991&message=OTP%3A+939005&messageID=OTP%3A+939005&shortcode=JuaMobile&mobile=254725887269','https://sms.movesms.co.ke/api/compose?username=lfhcompany&api_key=3cDj9t8dqkJayoQwG4IQcAIGObyUeccQTCTtPHY4nnIhqTkjT2&sender=SMARTLINK&to=254725887269&message=OTP%3A+939005&msgtype=5&dlr=0','2023-10-04 20:25:25','','');
DROP TABLE IF EXISTS staff;
CREATE TABLE `staff` (
  `staff_no` int(11) NOT NULL AUTO_INCREMENT,
  `staff_name` text NOT NULL,
  `staff_phone` text NOT NULL,
  `staff_email` text NOT NULL,
  `rate` text DEFAULT NULL,
  `joinDate` text NOT NULL,
  `ID_front` text NOT NULL,
  `ID_back` text NOT NULL,
  `passport_pic` text NOT NULL,
  `contract` text NOT NULL,
  `status` text NOT NULL DEFAULT 'active',
  `role` text NOT NULL DEFAULT 'staff',
  `exit_comment` text NOT NULL DEFAULT '\'none\'',
  `exited_date` text NOT NULL DEFAULT 'n/a',
  PRIMARY KEY (`staff_no`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO staff VALUES ('5','demo','254733440443','timnmburu@gmail.com','10','2023-08-14','fileStore/staff_docs/9.jpg','fileStore/staff_docs/9.jpg','fileStore/staff_docs/9.jpg','fileStore/staff_docs/9.jpg','active','admin','\'none\'','n/a');
INSERT INTO staff VALUES ('6','Timothy Njoroge','254725887269','timnmburu1@gmail.com','20','2023-10-09','fileStore/staff_docs/email debug.png','fileStore/staff_docs/email debug.png','fileStore/staff_docs/email debug.png','fileStore/staff_docs/email debug.png','active','admin','\'none\'','n/a');
DROP TABLE IF EXISTS target;
CREATE TABLE `target` (
  `monthlyTarget` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO target VALUES ('50000');
DROP TABLE IF EXISTS userlogs;
CREATE TABLE `userlogs` (
  `username` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO userlogs VALUES ('demo','2023-08-21 11:51:00');
INSERT INTO userlogs VALUES ('demo','2023-08-21 12:06:40');
INSERT INTO userlogs VALUES ('demo','2023-08-29 16:46:03');
INSERT INTO userlogs VALUES ('demo','2023-08-30 23:53:51');
INSERT INTO userlogs VALUES ('demo','2023-08-31 00:46:00');
INSERT INTO userlogs VALUES ('demo','2023-08-31 09:11:38');
INSERT INTO userlogs VALUES ('demo','2023-08-31 09:54:26');
INSERT INTO userlogs VALUES ('demo','2023-08-31 10:19:33');
INSERT INTO userlogs VALUES ('demo','2023-08-31 21:31:17');
INSERT INTO userlogs VALUES ('demo','2023-08-31 22:12:38');
INSERT INTO userlogs VALUES ('demo','2023-08-31 22:49:04');
INSERT INTO userlogs VALUES ('demo','2023-09-05 09:29:00');
INSERT INTO userlogs VALUES ('demo','2023-09-05 09:36:49');
INSERT INTO userlogs VALUES ('demo','2023-09-05 09:45:31');
INSERT INTO userlogs VALUES ('demo','2023-09-06 11:27:39');
INSERT INTO userlogs VALUES ('demo','2023-09-06 22:47:37');
INSERT INTO userlogs VALUES ('demo','2023-09-06 22:53:02');
INSERT INTO userlogs VALUES ('demo','2023-09-09 01:37:44');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:09:10');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:09:42');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:09:53');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:20:48');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:21:39');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:38:51');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:41:54');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:44:42');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:45:16');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:45:35');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:54:32');
INSERT INTO userlogs VALUES ('demo','2023-09-18 11:58:12');
INSERT INTO userlogs VALUES ('demo','2023-09-18 12:06:39');
INSERT INTO userlogs VALUES ('demo','2023-09-18 12:14:49');
INSERT INTO userlogs VALUES ('demo','2023-09-18 12:16:25');
INSERT INTO userlogs VALUES ('demo','2023-09-18 12:16:40');
INSERT INTO userlogs VALUES ('demo','2023-09-18 12:20:18');
INSERT INTO userlogs VALUES ('demo','2023-09-19 16:47:10');
INSERT INTO userlogs VALUES ('demo','2023-09-19 16:47:16');
INSERT INTO userlogs VALUES ('demo','2023-09-20 08:51:11');
INSERT INTO userlogs VALUES ('demo','2023-09-20 09:48:02');
INSERT INTO userlogs VALUES ('demo','2023-09-20 09:57:03');
INSERT INTO userlogs VALUES ('demo','2023-09-20 09:58:04');
INSERT INTO userlogs VALUES ('demo','2023-09-20 10:39:50');
INSERT INTO userlogs VALUES ('demo','2023-09-20 10:41:04');
INSERT INTO userlogs VALUES ('demo','2023-09-20 10:53:04');
INSERT INTO userlogs VALUES ('demo','2023-09-20 15:41:25');
INSERT INTO userlogs VALUES ('demo','2023-09-20 15:47:10');
INSERT INTO userlogs VALUES ('demo','2023-09-20 21:01:46');
INSERT INTO userlogs VALUES ('demo','2023-09-20 21:12:13');
INSERT INTO userlogs VALUES ('demo','2023-09-20 21:36:31');
INSERT INTO userlogs VALUES ('demo','2023-09-20 22:20:07');
INSERT INTO userlogs VALUES ('demo','2023-09-20 22:20:30');
INSERT INTO userlogs VALUES ('demo','2023-09-20 22:23:15');
INSERT INTO userlogs VALUES ('demo','2023-09-28 08:09:07');
INSERT INTO userlogs VALUES ('demo','2023-09-28 10:42:16');
INSERT INTO userlogs VALUES ('demo','2023-09-28 10:44:01');
INSERT INTO userlogs VALUES ('demo','2023-09-28 11:08:08');
INSERT INTO userlogs VALUES ('demo','2023-09-28 11:27:57');
INSERT INTO userlogs VALUES ('demo','2023-09-28 11:31:34');
INSERT INTO userlogs VALUES ('demo','2023-09-28 11:32:09');
INSERT INTO userlogs VALUES ('demo','2023-09-28 11:32:42');
INSERT INTO userlogs VALUES ('demo','2023-09-28 11:36:31');
INSERT INTO userlogs VALUES ('demo','2023-09-29 08:37:41');
INSERT INTO userlogs VALUES ('demo','2023-09-29 09:23:41');
INSERT INTO userlogs VALUES ('demo','2023-09-29 09:54:41');
INSERT INTO userlogs VALUES ('demo','2023-09-29 11:06:06');
INSERT INTO userlogs VALUES ('daante','2023-09-29 12:00:36');
INSERT INTO userlogs VALUES ('demo','2023-09-29 12:06:06');
INSERT INTO userlogs VALUES ('demo','2023-09-29 12:37:42');
INSERT INTO userlogs VALUES ('daante','2023-09-29 12:46:57');
INSERT INTO userlogs VALUES ('daante','2023-09-29 12:47:47');
INSERT INTO userlogs VALUES ('demo','2023-09-29 12:52:59');
INSERT INTO userlogs VALUES ('daa','2023-09-29 12:55:53');
INSERT INTO userlogs VALUES ('demo','2023-09-29 12:57:31');
INSERT INTO userlogs VALUES ('demo','2023-10-03 15:36:23');
INSERT INTO userlogs VALUES ('demo','2023-10-03 16:55:54');
INSERT INTO userlogs VALUES ('demo','2023-10-03 16:56:42');
INSERT INTO userlogs VALUES ('demo','2023-10-03 18:42:02');
INSERT INTO userlogs VALUES ('demo','2023-10-03 18:43:50');
INSERT INTO userlogs VALUES ('demo','2023-10-04 08:53:20');
INSERT INTO userlogs VALUES ('demo','2023-10-04 10:04:26');
INSERT INTO userlogs VALUES ('demo','2023-10-04 10:06:14');
INSERT INTO userlogs VALUES ('demo','2023-10-04 10:35:03');
INSERT INTO userlogs VALUES ('demo','2023-10-04 11:14:42');
INSERT INTO userlogs VALUES ('demo','2023-10-04 11:20:04');
INSERT INTO userlogs VALUES ('demo','2023-10-04 11:22:53');
INSERT INTO userlogs VALUES ('demo','2023-10-04 11:43:14');
INSERT INTO userlogs VALUES ('demo','2023-10-04 11:44:09');
INSERT INTO userlogs VALUES ('demo','2023-10-04 11:44:46');
INSERT INTO userlogs VALUES ('demo','2023-10-04 11:46:29');
INSERT INTO userlogs VALUES ('demo','2023-10-04 11:46:58');
INSERT INTO userlogs VALUES ('demo','2023-10-04 11:47:27');
INSERT INTO userlogs VALUES ('demo','2023-10-04 12:10:37');
INSERT INTO userlogs VALUES ('demo','2023-10-04 12:16:03');
INSERT INTO userlogs VALUES ('demo','2023-10-04 12:16:40');
INSERT INTO userlogs VALUES ('demo','2023-10-04 12:18:19');
INSERT INTO userlogs VALUES ('demo','2023-10-04 12:24:21');
INSERT INTO userlogs VALUES ('demo','2023-10-04 15:06:25');
INSERT INTO userlogs VALUES ('demo','2023-10-04 15:49:02');
INSERT INTO userlogs VALUES ('demo','2023-10-04 15:52:56');
INSERT INTO userlogs VALUES ('demo','2023-10-04 16:54:44');
INSERT INTO userlogs VALUES ('demo','2023-10-04 18:53:22');
INSERT INTO userlogs VALUES ('demo','2023-10-05 09:02:08');
INSERT INTO userlogs VALUES ('demo','2023-10-05 12:57:37');
INSERT INTO userlogs VALUES ('demo','2023-10-06 08:20:35');
INSERT INTO userlogs VALUES ('demo','2023-10-06 08:34:53');
INSERT INTO userlogs VALUES ('demo','2023-10-06 08:36:39');
INSERT INTO userlogs VALUES ('demo','2023-10-06 11:22:16');
INSERT INTO userlogs VALUES ('demo','2023-10-06 12:41:22');
INSERT INTO userlogs VALUES ('demo','2023-10-06 12:43:20');
INSERT INTO userlogs VALUES ('demo','2023-10-06 12:43:35');
INSERT INTO userlogs VALUES ('demo','2023-10-06 12:50:03');
INSERT INTO userlogs VALUES ('demo','2023-10-06 12:51:52');
INSERT INTO userlogs VALUES ('demo','2023-10-06 12:54:07');
INSERT INTO userlogs VALUES ('demo','2023-10-06 13:02:21');
INSERT INTO userlogs VALUES ('demo','2023-10-06 13:03:52');
INSERT INTO userlogs VALUES ('demo','2023-10-06 13:04:47');
INSERT INTO userlogs VALUES ('demo','2023-10-06 13:17:03');
INSERT INTO userlogs VALUES ('demo','2023-10-06 13:17:52');
INSERT INTO userlogs VALUES ('demo','2023-10-06 13:29:12');
INSERT INTO userlogs VALUES ('demo','2023-10-06 13:42:34');
INSERT INTO userlogs VALUES ('demo','2023-10-07 10:41:31');
INSERT INTO userlogs VALUES ('DEMO','2023-10-07 11:51:46');
INSERT INTO userlogs VALUES ('demo','2023-10-07 11:53:00');
INSERT INTO userlogs VALUES ('demo','2023-10-07 11:59:53');
INSERT INTO userlogs VALUES ('demo','2023-10-09 07:52:51');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:23:18');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:34:58');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:35:38');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:35:58');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:38:42');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:38:58');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:42:09');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:42:50');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:43:01');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:43:03');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:48:59');
INSERT INTO userlogs VALUES ('demo','2023-10-09 14:49:11');
INSERT INTO userlogs VALUES ('demo','2023-10-09 15:00:23');
INSERT INTO userlogs VALUES ('tim','2023-10-09 15:02:54');
INSERT INTO userlogs VALUES ('demo','2023-10-09 15:05:27');
INSERT INTO userlogs VALUES ('demo','2023-10-09 15:16:39');
INSERT INTO userlogs VALUES ('demo','2023-10-09 15:25:41');
INSERT INTO userlogs VALUES ('tim','2023-10-09 16:25:23');
INSERT INTO userlogs VALUES ('tim','2023-10-09 16:41:44');
INSERT INTO userlogs VALUES ('tim','2023-11-03 10:26:07');
INSERT INTO userlogs VALUES ('tim','2023-11-03 10:47:25');
INSERT INTO userlogs VALUES ('tim','2023-11-03 10:48:57');
INSERT INTO userlogs VALUES ('demo','2023-11-03 10:49:51');
INSERT INTO userlogs VALUES ('tim','2023-11-03 11:03:29');
INSERT INTO userlogs VALUES ('demo','2023-11-03 11:04:01');
INSERT INTO userlogs VALUES ('tim','2023-11-03 11:10:18');
INSERT INTO userlogs VALUES ('demo','2023-11-03 11:10:59');
INSERT INTO userlogs VALUES ('tim','2023-11-03 11:13:15');
INSERT INTO userlogs VALUES ('demo','2023-11-03 11:13:38');
DROP TABLE IF EXISTS users;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `phone` text DEFAULT NULL,
  `token` text NOT NULL,
  `lastResetDate` text NOT NULL DEFAULT current_timestamp(),
  `api_key` text DEFAULT NULL,
  `role` text DEFAULT NULL,
  `custID` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO users VALUES ('1','millie','$2y$10$y9kSViAVvu7XekLA0XWTC.Hh7pHr6a0VmX.s0yr63M4cOcosXQ1tG','mmwatetu@gmail.com','','','2023-06-22 23:14:36','','','2301');
INSERT INTO users VALUES ('7','demo','vWnep1F66g0ByEz5m29m/qbsirrNRFA9DAEeRBNyQniOIfJTV5ZxZal2gYJ+jvacrKmAYk/xc2dMk8rpYS+RX/gcde51H6tiQbgjLMLI36M4HfsaSxKRI16oXtCgBAfF','timnmburu@gmail.com','254733440443','','2023-10-07 10:41:07','12gdfgg.3478','NULL','2301');
INSERT INTO users VALUES ('13','tim','KuVfV5fwXJWhWoirLHxyLgPGq/HPCuaJa/v1Smdbz1vAbl4GBb0b/4Q0A7iZpUvm4zSB4djY1ZZ4a7qbMCdkiDRDAOt8k6u318kcBuBN8RR9Uf+s7yXVzy7IXk9XKNLT','timnmburu1@gmail.com','254725887269','','2023-10-09 15:01:18','','admin','2301');
DROP TABLE IF EXISTS wallet;
CREATE TABLE `wallet` (
  `mpesa` text NOT NULL,
  `kcb` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO wallet VALUES ('56734','765732');
