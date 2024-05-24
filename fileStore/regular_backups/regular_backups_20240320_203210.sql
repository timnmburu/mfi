DROP TABLE IF EXISTS account;
CREATE TABLE `account` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `custID` mediumtext NOT NULL,
  `custName` mediumtext NOT NULL,
  `custPhone` mediumtext NOT NULL,
  `subDate` mediumtext NOT NULL,
  `subAmount` mediumtext NOT NULL,
  `lastPayAmount` mediumtext NOT NULL,
  `lastPayDate` mediumtext NOT NULL,
  `nextPayDate` mediumtext NOT NULL,
  `status` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO account VALUES ('1','2404','ShtvLN6Kmz9c2n5rDYkS77FfxPFprDIY2bSI/Pk/PkRJJnusYSWAU2d5L1IC7Plh+TKzVBvqGArMmRgA83vR++xk9rdfoMFPvXGGaKcnZME/Amho8yLymVxl1ov9ddKp','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS78Ispkh8pVWjlVgxJVYJNM1s1Qlxp5fwlR8jFEKhdxyRsBqIOeYxGi0FcbA6k75Dq5rwlWFTTf48OtbJBIUiOkv9Z2eESDV66OtmUDCBBzpn','ShtvLN6Kmz9c2n5rDYkS71EeI7Ya3BXWpE0p7X1B+jj0J3Fu19p8ETujdANWNx0cJP4pmNpWcIl5uV16oaEXqHn2yahyfk2s23HGCTjReXx/R4CRI4ZRj5ia1xs3dlyT','ShtvLN6Kmz9c2n5rDYkS71EeI7Ya3BXWpE0p7X1B+jj0J3Fu19p8ETujdANWNx0cJP4pmNpWcIl5uV16oaEXqHn2yahyfk2s23HGCTjReXx/R4CRI4ZRj5ia1xs3dlyT','ShtvLN6Kmz9c2n5rDYkS78XC0TiX2PCLYRzgMFPIpaZFEAxcHqh4+1+KRb+DsJFziCGy0KhwnDLSJl3E1MwhUXx82taUJEl2PQK16eoUtqH117rUULqWjE55xBnckW4L','ShtvLN6Kmz9c2n5rDYkS77PDJ86SqSfywCxvsSA0H1whs+YzPH27G/PNkreHvR3Enrbe+RnI/WSAmb/BMRacywJulbs7rQuYxhzszD3ad7L6mmL9W+djE2rs6g633Ihr','ShtvLN6Kmz9c2n5rDYkS79j+n9DU5XjUyTYLjIEpehTJWm/i8Y6WH/dcQ2CxsCkOmJf/trKCksW6DcBUtB3OBchzC0A6imi/xAfzzDgX2f1zdRpSdKCwDv9324QGRgyT');
DROP TABLE IF EXISTS bankCodes;
CREATE TABLE `bankCodes` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `bank_name` mediumtext NOT NULL,
  `bank_code` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO bankCodes VALUES ('1','KCB','1');
INSERT INTO bankCodes VALUES ('2','Standard Charted Bank KE','2');
INSERT INTO bankCodes VALUES ('3','Barclays Bank','3');
INSERT INTO bankCodes VALUES ('4','NCBA','7');
INSERT INTO bankCodes VALUES ('5','Prime Bank','10');
INSERT INTO bankCodes VALUES ('6','Cooperative Bank','11');
INSERT INTO bankCodes VALUES ('7','National Bank','12');
INSERT INTO bankCodes VALUES ('8','Citibank','16');
INSERT INTO bankCodes VALUES ('9','Habib Bank AG Zurich','17');
INSERT INTO bankCodes VALUES ('10','Middle East Bank','18');
INSERT INTO bankCodes VALUES ('11','Bank of Africa','19');
INSERT INTO bankCodes VALUES ('12','Consolidated Bank','23');
INSERT INTO bankCodes VALUES ('13','Credit Bank Ltd','25');
INSERT INTO bankCodes VALUES ('14','Stanbic Bank','31');
INSERT INTO bankCodes VALUES ('15','ABC Bank','35');
INSERT INTO bankCodes VALUES ('16','Spire Bank','49');
INSERT INTO bankCodes VALUES ('17','Paramount Universal Bank','50');
INSERT INTO bankCodes VALUES ('18','Jamii Bora Bank','51');
INSERT INTO bankCodes VALUES ('19','Guaranty Bank','53');
INSERT INTO bankCodes VALUES ('20','Victoria Commercial Bank','54');
INSERT INTO bankCodes VALUES ('21','Guardian Bank','55');
INSERT INTO bankCodes VALUES ('22','I&M Bank','57');
INSERT INTO bankCodes VALUES ('23','DTB','63');
INSERT INTO bankCodes VALUES ('24','Sidian Bank','66');
INSERT INTO bankCodes VALUES ('25','Equity Bank','68');
INSERT INTO bankCodes VALUES ('26','Family Bank','70');
INSERT INTO bankCodes VALUES ('27','Gulf African Bank','72');
INSERT INTO bankCodes VALUES ('28','First Community Bank','74');
INSERT INTO bankCodes VALUES ('29','KWFT Bank','78');
INSERT INTO bankCodes VALUES ('30','Housing Finance Company Limited (HFCK)','61');
INSERT INTO bankCodes VALUES ('31','Mayfair Bank Limited','65');
DROP TABLE IF EXISTS commission_payments;
CREATE TABLE `commission_payments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `phone` mediumtext NOT NULL,
  `amount` mediumtext NOT NULL,
  `accBal` mediumtext NOT NULL,
  `date` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS counter;
CREATE TABLE `counter` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `new_customer_count` mediumtext DEFAULT NULL,
  `repeat_customer_count` mediumtext DEFAULT NULL,
  `new_bookings_count` mediumtext DEFAULT NULL,
  `new_orders_count` mediumtext DEFAULT NULL,
  `month_year` mediumtext DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS customer_registration;
CREATE TABLE `customer_registration` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `loan_no` text DEFAULT NULL,
  `invoice_id` mediumtext NOT NULL,
  `state` mediumtext NOT NULL,
  `provider` mediumtext NOT NULL,
  `charges` mediumtext NOT NULL,
  `net_amount` mediumtext NOT NULL,
  `value` mediumtext NOT NULL,
  `account` mediumtext NOT NULL,
  `api_ref` mediumtext NOT NULL,
  `clearing_status` mediumtext DEFAULT NULL,
  `mpesa_reference` mediumtext DEFAULT NULL,
  `failed_reason` mediumtext DEFAULT NULL,
  `failed_code` mediumtext DEFAULT NULL,
  `date` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO customer_registration VALUES ('2','2','Y5PDOPY','COMPLETE','M-PESA','0.36','11.64','12.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCG29MKHJU','','','2023-03-16 21:10:37');
INSERT INTO customer_registration VALUES ('3','3','04XZ450','COMPLETE','M-PESA','0.33','10.67','11.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCG89SD8KK','','','2024-03-16 21:57:28');
INSERT INTO customer_registration VALUES ('4','4','08JVP60','COMPLETE','M-PESA','0.33','10.67','11.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCJ1I2B8D9','','','2024-03-19 12:57:03');
DROP TABLE IF EXISTS customers;
CREATE TABLE `customers` (
  `customer_no` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` mediumtext NOT NULL,
  `customer_phone` mediumtext NOT NULL,
  `customer_idno` text DEFAULT NULL,
  `customer_email` mediumtext NOT NULL,
  `joinDate` mediumtext NOT NULL,
  `ID_front` mediumtext NOT NULL,
  `ID_back` mediumtext NOT NULL,
  `passport_pic` mediumtext NOT NULL,
  `contract` mediumtext NOT NULL,
  `status` mediumtext DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  `mpesa_registration_code` text DEFAULT NULL,
  PRIMARY KEY (`customer_no`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO customers VALUES ('1','ShtvLN6Kmz9c2n5rDYkS74OtBJLWrMJ0g/d09kNlMHa4BQVl5Zh0nDVCWjEXBYkIzN7K05ROctAdvAkfE6n9xPJ2sTsSUNnE9FNskVM6c5QbJxnNmg939PW2dD8/wF2I','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS75kycHWlCywyHwmmV06FgQ3aredCOf2NimuAt9Fxwz9u/0y/UZH3CsPsKpg0Zxt/I0eEKFhhk7KboSzJ31Xh7foMqB/b4a8u0JcSQbo6CEmh','ShtvLN6Kmz9c2n5rDYkS75a5OKthKl3ac61/Oj8bLIL1+broXIztPjmzjY9+vBLviXqRBc9sr78qK7RIaqI0ANg2Hhqp4CgPQ2OFGAyzKeblbhUIM4guuVAN66kTSPO+pLdZRNJ8rnXtD6xo9lIKNw==','ShtvLN6Kmz9c2n5rDYkS72P7UlZ0CNvDYoaUmeehRxCOfOwn+IASK7lleHvDj7aIDwBTC7tY5XW7o1tbXkNr6HfrRp8lcIZneEZzEE/VxjzUDXm5iVMLOnnVBfrszvyB','ShtvLN6Kmz9c2n5rDYkS7zFMAHg/eiW73PkwRl4ZCKIKasMjeen4jXnR7rka4/pTBOjRxyDAcom5hn9plpZHecXQWkHHrtST0VJ9oN0W87a3rgq3KvtEMQ4O+TclO2P4CVSXNWYP1gli/0Gol16kh/9CSHvcXUZJaJ1ltzt/bhg=','ShtvLN6Kmz9c2n5rDYkS71Pnh8smYLWbQ9YL1oIlJWH5ykNteR2kSvxyCB6yZqmk/9zFPPzSNsXMZjRLBdWIenmJHC0rT1Axi8Cl7eIfiZK3rgq3KvtEMQ4O+TclO2P4rqCBPYlriAn4njfXTFuFWejfZCdLIb5YmucSd7cYV7k=','ShtvLN6Kmz9c2n5rDYkS7yrqYYrSifKe9ozLtNz1+r7Jhir56SFA6mwcQ2I2Qv3l2YLjaljOtQVSVMmtM1KzEBS9qpgoCQ8t5BMnro4Pz1O3rgq3KvtEMQ4O+TclO2P4eR973A79fLpwmRJQJWtco/bnIWxWc6npDsOBc6t96+M26OGa1m2/xuXVxFDWPMXG','ShtvLN6Kmz9c2n5rDYkS79SogmIKC4F37BK2NoU0mmN27lqIQvvdGoexuE/yKxMTVZyZU+Od2OJu+xn0j9C3sWQftmkZZImVeM6CBltjeny3rgq3KvtEMQ4O+TclO2P40Jwubq33S+MKAs4cWmq9Y4T5RKnd1YRB4ENARwTxr9s=','ShtvLN6Kmz9c2n5rDYkS79pgqR5FQ068SKGSN8Qt4d+nuvykanvidfH/7v0wZXe37j3UIR+9wubJFPoICaLJ8j2MVVvvVUBDIyTlGCuywSAqynCd3RnR8Imhp2/Q+03o','Machakos','');
INSERT INTO customers VALUES ('2','ShtvLN6Kmz9c2n5rDYkS7yUGReqI3sFelAk06Q8Jc6o4YwtMY5I7Ug5BHp2PDHWEHLnDeWSZ6Ho16/4bYRZIa+qPAoTv7Yue5QdimF1pQBbfIWZHXK7cmoNWDQQxWTOr','ShtvLN6Kmz9c2n5rDYkS78db4bXU1aDT8JjTUXZYMBnuzwHt8a1isLzUTrMvpw/V4fiB+tbdJt1vdpeuvDblN3Pj3DGFe2kT0Kqgc2q0Gx+Lsmd07Oqi20xj9a6oIvMR','ShtvLN6Kmz9c2n5rDYkS75IAjJq1VFpTT/oV52MfhkhNoxVoAbLWZV4XbEUXZNKdWktGNYsRUt35EOKxBx59bU8Ll0RIn1aSDHdpe/gIDAbHJFxpyzwGRdwKVW6epnBI','ShtvLN6Kmz9c2n5rDYkS7xYWOVktiACK3CuY2/F8yHYJpR/dCiEnHStVqcxSZOl6PM4uG2wcV7mcMqPfwfeFmq3uvQf103ddUVYzKoeMDQtX7LU9wurUsX6aJ7oBdOny3zmfGN3xI8ySENbBB1bmtQ==','ShtvLN6Kmz9c2n5rDYkS7/dGFx9FFQzEZxGVwlTHJ37fFWT4vbIKdeLbAIpeM3bsv/guESIc5RxYBdPbEGo6Ir7IxYfcXvAgRvQqKMxpTLZmPSi7NqNG8brNYgAVdyYA','ShtvLN6Kmz9c2n5rDYkS7y6kbVzZrmP/UQU2AL4U/KYu7sGKXIkrgYE35ykO7tZaLRcZPN+OtMhnGHB7mgxaSZkiLFIxS06++mJLOwkPYje3rgq3KvtEMQ4O+TclO2P4CVSXNWYP1gli/0Gol16khwVZf8mQqnRGH4+JaCwvPOk=','ShtvLN6Kmz9c2n5rDYkS7wC2UFIC724gQoVe3IOwqiUZevPBxYWSOvHOwzTaFH3F1t/BdbtlLw7XZdWe9yauyV48YxsyM1qzrH0mE1JyB+W3rgq3KvtEMQ4O+TclO2P4rqCBPYlriAn4njfXTFuFWaC750MZgI94kNx2f4HPe+k=','ShtvLN6Kmz9c2n5rDYkS77vMOE8mWKkEPc5hdcl4EgW3eV6VpCmbSLts+YRCp47o2ISwYPth48mkDv6cUjW5DHNqz+QOzIw+e/WS3/8d10a3rgq3KvtEMQ4O+TclO2P4eR973A79fLpwmRJQJWtco9PbtblWwt7Nv+9eyJBGJG+7wH7h0oKzIwukeo6ReHB8','ShtvLN6Kmz9c2n5rDYkS75Y841dxF0po8u23JayM/Q6Vm1cY1yi0SkKEY8I6QGQDlxUsmNQLMmVkDEED4enFkq+IJp1xc9KwEBkWTJ6hmTa3rgq3KvtEMQ4O+TclO2P40Jwubq33S+MKAs4cWmq9Y9fBlYz1BF2oUueTSjfpE8Q=','ShtvLN6Kmz9c2n5rDYkS79pgqR5FQ068SKGSN8Qt4d+nuvykanvidfH/7v0wZXe37j3UIR+9wubJFPoICaLJ8j2MVVvvVUBDIyTlGCuywSAqynCd3RnR8Imhp2/Q+03o','Tala','');
DROP TABLE IF EXISTS deposits;
CREATE TABLE `deposits` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `staff_no` mediumtext DEFAULT NULL,
  `name` mediumtext DEFAULT NULL,
  `phone` mediumtext DEFAULT NULL,
  `gross_amount` mediumtext DEFAULT NULL,
  `charge` mediumtext DEFAULT NULL,
  `net_amount` mediumtext DEFAULT NULL,
  `ref_no` mediumtext DEFAULT NULL,
  `date` mediumtext DEFAULT NULL,
  `payment_mode` mediumtext DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS expenseHistory;
CREATE TABLE `expenseHistory` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `amount` mediumtext NOT NULL,
  `date` mediumtext NOT NULL,
  `currentTotal` mediumtext NOT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS expenses;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `price` mediumtext NOT NULL,
  `quantity` mediumtext NOT NULL,
  `date` mediumtext NOT NULL,
  `paidFrom` mediumtext NOT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS feedback;
CREATE TABLE `feedback` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `email` mediumtext NOT NULL,
  `comment` mediumtext NOT NULL,
  `date` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS frequentPayments;
CREATE TABLE `frequentPayments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `wallet` mediumtext NOT NULL,
  `reference` mediumtext NOT NULL,
  `account` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS images;
CREATE TABLE `images` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` mediumtext NOT NULL,
  `time` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS inventory;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `price` mediumtext NOT NULL,
  `quantity` mediumtext NOT NULL,
  `date` mediumtext NOT NULL,
  `paidFrom` mediumtext NOT NULL,
  `itemCategory` mediumtext NOT NULL,
  `supplier` mediumtext NOT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS inventory_category;
CREATE TABLE `inventory_category` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `location_name` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS inventory_suppliers;
CREATE TABLE `inventory_suppliers` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `phone` mediumtext NOT NULL,
  `email` mediumtext NOT NULL,
  `location` mediumtext NOT NULL,
  `joinedDate` mediumtext NOT NULL,
  `location_name` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS loan_applications;
CREATE TABLE `loan_applications` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `loan_no` text DEFAULT NULL,
  `customer_no` mediumtext DEFAULT NULL,
  `customer_name` mediumtext DEFAULT NULL,
  `customer_phone` mediumtext DEFAULT NULL,
  `loan_product` mediumtext DEFAULT NULL,
  `loan_type` text DEFAULT NULL,
  `loan_amount` mediumtext DEFAULT NULL,
  `loan_term` mediumtext DEFAULT NULL,
  `no_of_installments` text DEFAULT NULL,
  `loan_interest` mediumtext DEFAULT NULL,
  `loan_installment` mediumtext DEFAULT NULL,
  `principalBal` text DEFAULT NULL,
  `interestBal` text DEFAULT NULL,
  `gross_loan` mediumtext DEFAULT NULL,
  `loan_balance` mediumtext DEFAULT NULL,
  `loan_applicationDate` mediumtext DEFAULT NULL,
  `take_home` mediumtext DEFAULT NULL,
  `loan_reviewer` mediumtext DEFAULT NULL,
  `loan_reviewDate` mediumtext DEFAULT NULL,
  `loan_approver` mediumtext DEFAULT NULL,
  `loan_approvalDate` mediumtext DEFAULT NULL,
  `loan_payments` mediumtext DEFAULT NULL,
  `firstRepaymentDate` text DEFAULT NULL,
  `repaymentFrequency` text DEFAULT NULL,
  `last_paymentDate` mediumtext DEFAULT NULL,
  `loan_status` mediumtext DEFAULT NULL,
  `loan_form` text DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  `loan_classification` mediumtext DEFAULT NULL,
  `days_inArrears` mediumtext DEFAULT NULL,
  `amount_inArrears` mediumtext DEFAULT NULL,
  `worst_classification` mediumtext DEFAULT NULL,
  `worst_daysInArrears` mediumtext DEFAULT NULL,
  `loan_writeoff` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO loan_applications VALUES ('1','1','1','ShtvLN6Kmz9c2n5rDYkS74OtBJLWrMJ0g/d09kNlMHa4BQVl5Zh0nDVCWjEXBYkIzN7K05ROctAdvAkfE6n9xPJ2sTsSUNnE9FNskVM6c5QbJxnNmg939PW2dD8/wF2I','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS77oPmOGxuU4c6wSOPXw9ZxQ77eSoGQfh0W2McsG4lzrEjHM4DX5RxjNXBu/gZdj9aWHlJApove9qrQ0gkGeW0Rf0LaubMQEIHdLf6VRLU4PP','ShtvLN6Kmz9c2n5rDYkS715mOMyO+MP5n+QGGDuqypvO1XBuGIcA0NKmBO3wtaQNxH8lM/Pd85xME6yuVGb5TnVWRlTPhpfVSOgarH9YaYdyirEwLQHG20QA0Nx/3p1E','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS74F/t0sPLXYaPWaPmiaEu2bH2PaA1yS7gJPixbhtG4mGVaiJlihEGuo33EI+nVhfBala4oHwvv/t/quuNPs6egYEbM28O7JDyKzrMqcSdcNV','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS78As+glD9pxV4PCKmgvrTGuP4cWX+NR6euMaPQQWmIkqdZrsGxMaIu2s7FqPyhfENJQD8g4xnEmQZeU/RIT0rQzmVxvUc8B5RWVkM1FpkY39uqTkT68ntO8p6CXjAq4EmAU9WLmjZZaQlj5R1k3jTiAf+KDRCRFpcpcV1VpIETvdRmTHosFRkewp7RC5An7xPWyrpi5t6SvkEQ70AqJBYa67iWRWH6BOZs22U1AqfwzwOHU+RhxA2v2ebGxSSfnWBz2dJUZpwnON5l8hcqBu8LQ=','','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS7wOrL49C2UQXp2gUgLOsGDN5ApS4YxhaCbK6kKn53N8s0moVmaid9o1YhKDCFZ6U3FfKlVDp6dfghAYVjsi0IRKzaejJyKFpM27PSgx5SgV7mCny/iDs5JjF6fEJGj28Gg==','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7wF6tLe7rRgz2ttag2XKZINgGfGG5VUYSeg3pbNFVq3a4A6KYWaZDTiJ09bMBdg6Jg4i2sQSrhk1Uj5GuAAwLJ/NLWrwRoLS2kH3ocop/1ml0LOA2zVIL/GsT50nPtTItA==','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS79yszrUsAqeQV/RkjFVSpcBEeN6Ai/7dJuREgsfbEOr2JrHI+n2o9coH4YPMdPSEU5T94mT498mPsEyasVcG6AaDCjKHsV9MY+isokvWp6uifRwtolIcJd4LRbJqrnBAuA==','','ShtvLN6Kmz9c2n5rDYkS71Z8dYIK75mwdp4LZKyX5ISPZxnoC8TLMBa7eQtXO8gqTtZCDJaI8+hJgtjmgEvgi3JBz/85OW3kXL85ImCxToSRLYU5UVGy+NMMJNQtWXNq','ShtvLN6Kmz9c2n5rDYkS77Vxs+7JnSqh7gdTdvExya8RVOO0JQ3Ye0k1kLcgo8ripq82Mur9HM5AWPKnlw6qznRbIMXFO6nD231fWFpCcwQB/F+vceKTAkTZbZEGkCFR','','ShtvLN6Kmz9c2n5rDYkS72LzM2Ag+keG4F2NrdRv0gDyHA6wCVYOPK2xBjUpj8VW88yUi4k8J4cBSlV5dtbFXS2ptWb12IPI7TTQJT8Xobee1a7Tv3c7a9J/bNBDD9Xy','ShtvLN6Kmz9c2n5rDYkS7xRvoTiCJ5MTm5G6w2wSraE3TT0vxVepRlU8TqFaGUByiDA/rRv1VU5ngZre8tNiTFsdisNTVTvQfRBn5a1jyNy3rgq3KvtEMQ4O+TclO2P45BIX6IkCKaSj02o6cK+hQ78Bfr0lguIv8pGCtoetPTN4VMHiJF8+4Rvlj/bXtBlq','Machakos','','','','','','');
INSERT INTO loan_applications VALUES ('2','2','1','ShtvLN6Kmz9c2n5rDYkS74OtBJLWrMJ0g/d09kNlMHa4BQVl5Zh0nDVCWjEXBYkIzN7K05ROctAdvAkfE6n9xPJ2sTsSUNnE9FNskVM6c5QbJxnNmg939PW2dD8/wF2I','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS77oPmOGxuU4c6wSOPXw9ZxQ77eSoGQfh0W2McsG4lzrEjHM4DX5RxjNXBu/gZdj9aWHlJApove9qrQ0gkGeW0Rf0LaubMQEIHdLf6VRLU4PP','ShtvLN6Kmz9c2n5rDYkS715mOMyO+MP5n+QGGDuqypvO1XBuGIcA0NKmBO3wtaQNxH8lM/Pd85xME6yuVGb5TnVWRlTPhpfVSOgarH9YaYdyirEwLQHG20QA0Nx/3p1E','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS74F/t0sPLXYaPWaPmiaEu2bH2PaA1yS7gJPixbhtG4mGVaiJlihEGuo33EI+nVhfBala4oHwvv/t/quuNPs6egYEbM28O7JDyKzrMqcSdcNV','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS78As+glD9pxV4PCKmgvrTGuP4cWX+NR6euMaPQQWmIkqdZrsGxMaIu2s7FqPyhfENJQD8g4xnEmQZeU/RIT0rQzmVxvUc8B5RWVkM1FpkY39uqTkT68ntO8p6CXjAq4EmAU9WLmjZZaQlj5R1k3jTiAf+KDRCRFpcpcV1VpIETvdRmTHosFRkewp7RC5An7xPWyrpi5t6SvkEQ70AqJBYa67iWRWH6BOZs22U1AqfwzwOHU+RhxA2v2ebGxSSfnWBz2dJUZpwnON5l8hcqBu8LQ=','','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS78n6i4y9r8EHoo2uSyHmsRU5yrTabSzE0aenD/ygO9Hhgj0lxcsqIlf/1mh88DkGeIqQ8qA/Kt4DXnH3TUtl+HaPc+qytQHtGNjgy68ONTe5NG34XAaVDfP/sbZzLk8ivg==','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS70V0nc0BENVeVKgVpq90tR1k3Wug+FIlitfW40vql3Bth0CgmqLbD7QynaqXq/eqSQ3iBt/fbNY4aqMaA5BLdgyPc+qytQHtGNjgy68ONTe5g6YyxOTp6U++vcmp36nJ5g==','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS77JDyjRtubwU7wLaSIr/r1Af/RUZyCNkE4M/Mju6p2/5LFweIA399eH8jRlAtXzArjvBGmhjAz/s9Xon9JfQXJiPc+qytQHtGNjgy68ONTe5J8lFm/vJbQQlzyczSo2+mw==','','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS77Vxs+7JnSqh7gdTdvExya8RVOO0JQ3Ye0k1kLcgo8ripq82Mur9HM5AWPKnlw6qznRbIMXFO6nD231fWFpCcwQB/F+vceKTAkTZbZEGkCFR','','ShtvLN6Kmz9c2n5rDYkS72LzM2Ag+keG4F2NrdRv0gDyHA6wCVYOPK2xBjUpj8VW88yUi4k8J4cBSlV5dtbFXS2ptWb12IPI7TTQJT8Xobee1a7Tv3c7a9J/bNBDD9Xy','ShtvLN6Kmz9c2n5rDYkS72S0yIwBeUSEAL6jdRBHb83KDPHqkkG24fKcOQBd4xALsfovWXSgqGnxcbWBbBZ8dNAAGpFf4CZ7hMx3bG8MQJO3rgq3KvtEMQ4O+TclO2P45BIX6IkCKaSj02o6cK+hQ18QGm6YmQLoyrDmIzPXOMmPtJoLJtHNmLSJGrwKLBJM','Machakos','','','','','','');
INSERT INTO loan_applications VALUES ('3','3','1','ShtvLN6Kmz9c2n5rDYkS74OtBJLWrMJ0g/d09kNlMHa4BQVl5Zh0nDVCWjEXBYkIzN7K05ROctAdvAkfE6n9xPJ2sTsSUNnE9FNskVM6c5QbJxnNmg939PW2dD8/wF2I','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS77oPmOGxuU4c6wSOPXw9ZxQ77eSoGQfh0W2McsG4lzrEjHM4DX5RxjNXBu/gZdj9aWHlJApove9qrQ0gkGeW0Rf0LaubMQEIHdLf6VRLU4PP','ShtvLN6Kmz9c2n5rDYkS7wQPSwJBxdAP9JoFEmroh60jSIIa3E3ucB300xW6LEoxumqiCSRFb8Q0xgbCyOEtJ2tHEez0qJIopubkqPypLKkiKO+reXmG+G5fkukBLShG','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS74F/t0sPLXYaPWaPmiaEu2bH2PaA1yS7gJPixbhtG4mGVaiJlihEGuo33EI+nVhfBala4oHwvv/t/quuNPs6egYEbM28O7JDyKzrMqcSdcNV','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS78As+glD9pxV4PCKmgvrTGuP4cWX+NR6euMaPQQWmIkqdZrsGxMaIu2s7FqPyhfENJQD8g4xnEmQZeU/RIT0rQzmVxvUc8B5RWVkM1FpkY39uqTkT68ntO8p6CXjAq4EmAU9WLmjZZaQlj5R1k3jTiAf+KDRCRFpcpcV1VpIETvdRmTHosFRkewp7RC5An7xPWyrpi5t6SvkEQ70AqJBYa67iWRWH6BOZs22U1AqfwzwOHU+RhxA2v2ebGxSSfnWBz2dJUZpwnON5l8hcqBu8LQ=','','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS70rWqntzKYOzKBEbCVOLYJ+sZf89qYZzM1u9qJQWvgUiNyX61QBJrpWOMlFqwl98KKwJSgOigk9db/098gOZIuEnK7dYjk0JJqpxnR7IzjLyJE8TWwX/jVQ2xxH0XSct7w==','ShtvLN6Kmz9c2n5rDYkS77xgfjrXomEtFpXYnBM3vdZUoI06WvLpVkOjvTFTm0XKns1MbALJFHPQ0UoeJXPXhQ46SH9fFyvy5E3YLYYRA4ePq1CoET94zCSRqqHZjgqw','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS72rH1RDQgj08T8RMMySWPiH0dXRdP4Hu9SfyYhsNe53+pI6kdm/cnRiGASG3buZiWYVoTRluf5UEs6Pku5n3yOtNPfICNZq+uBEWMjHI3w6I6erqTr0nvnxlaH3eS7THdg==','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7ytgaubSjWGgveq8JemUQivvtcJeBJ+VCYET/rtQLmIvHV43DtLPo4Cpd9jGxxwK0GILqqmqIiN43+rMkVPw7eJNPfICNZq+uBEWMjHI3w6IH/XkuO2wLRLqx4p86gLj0w==','','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS77Vxs+7JnSqh7gdTdvExya8RVOO0JQ3Ye0k1kLcgo8ripq82Mur9HM5AWPKnlw6qznRbIMXFO6nD231fWFpCcwQB/F+vceKTAkTZbZEGkCFR','','ShtvLN6Kmz9c2n5rDYkS72LzM2Ag+keG4F2NrdRv0gDyHA6wCVYOPK2xBjUpj8VW88yUi4k8J4cBSlV5dtbFXS2ptWb12IPI7TTQJT8Xobee1a7Tv3c7a9J/bNBDD9Xy','ShtvLN6Kmz9c2n5rDYkS786mVvfaJFpDCUOKPvChSJNN1Lph12ISKrWn2JxV5l/F4oJwKMUC5i8mSAIDm4LPQdMpVQF7KtH0lqiQBKJIwGu3rgq3KvtEMQ4O+TclO2P45BIX6IkCKaSj02o6cK+hQ4fhLj2QI+axNfbWDXJ5NhbJvz02SYhGypfRqAcKNoE2','Machakos','','','','','','');
INSERT INTO loan_applications VALUES ('4','4','1','ShtvLN6Kmz9c2n5rDYkS74OtBJLWrMJ0g/d09kNlMHa4BQVl5Zh0nDVCWjEXBYkIzN7K05ROctAdvAkfE6n9xPJ2sTsSUNnE9FNskVM6c5QbJxnNmg939PW2dD8/wF2I','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS77oPmOGxuU4c6wSOPXw9ZxQ77eSoGQfh0W2McsG4lzrEjHM4DX5RxjNXBu/gZdj9aWHlJApove9qrQ0gkGeW0Rf0LaubMQEIHdLf6VRLU4PP','ShtvLN6Kmz9c2n5rDYkS715mOMyO+MP5n+QGGDuqypvO1XBuGIcA0NKmBO3wtaQNxH8lM/Pd85xME6yuVGb5TnVWRlTPhpfVSOgarH9YaYdyirEwLQHG20QA0Nx/3p1E','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS74F/t0sPLXYaPWaPmiaEu2bH2PaA1yS7gJPixbhtG4mGVaiJlihEGuo33EI+nVhfBala4oHwvv/t/quuNPs6egYEbM28O7JDyKzrMqcSdcNV','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS78As+glD9pxV4PCKmgvrTGuP4cWX+NR6euMaPQQWmIkqdZrsGxMaIu2s7FqPyhfENJQD8g4xnEmQZeU/RIT0rQzmVxvUc8B5RWVkM1FpkY39uqTkT68ntO8p6CXjAq4EmAU9WLmjZZaQlj5R1k3jTiAf+KDRCRFpcpcV1VpIETvdRmTHosFRkewp7RC5An7xPWyrpi5t6SvkEQ70AqJBYa67iWRWH6BOZs22U1AqfwzwOHU+RhxA2v2ebGxSSfnWBz2dJUZpwnON5l8hcqBu8LQ=','','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS71Vj5lA9Jq0eb2o78wcSL+3DwSiK6ha5kiCHGAGw8r3t8zfTdXgZsJPRlnpEEHmXTkIjGQR+VSFmerTQqqUEa7r27Y7fWk+tJ+i3YOAJCW9Vg3vv8hA+79GZg/2FejO9Hw==','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7zBTKyFvyWE0llOwdmSVCoT2pmssH8+MNlMbqMp6oCCJAybV091fdrAO7CiCtC0VRbp5UxfWZEhloFS4oTKU52f27Y7fWk+tJ+i3YOAJCW9Vdss+Ixp9yIS4RX3SpaCobA==','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS70D5soVuRJYEmiwnaQvbzkS9KX/Q79jHwKdMXX1JAl+1ot+wEW1cGPfflrjgIE1WzYrEyF1EZ0ej5QBHfrm4cjr27Y7fWk+tJ+i3YOAJCW9VowAzNtQ46vUch7I3uuxkVQ==','','ShtvLN6Kmz9c2n5rDYkS78Ispkh8pVWjlVgxJVYJNM1s1Qlxp5fwlR8jFEKhdxyRsBqIOeYxGi0FcbA6k75Dq5rwlWFTTf48OtbJBIUiOkv9Z2eESDV66OtmUDCBBzpn','ShtvLN6Kmz9c2n5rDYkS77Vxs+7JnSqh7gdTdvExya8RVOO0JQ3Ye0k1kLcgo8ripq82Mur9HM5AWPKnlw6qznRbIMXFO6nD231fWFpCcwQB/F+vceKTAkTZbZEGkCFR','','ShtvLN6Kmz9c2n5rDYkS72LzM2Ag+keG4F2NrdRv0gDyHA6wCVYOPK2xBjUpj8VW88yUi4k8J4cBSlV5dtbFXS2ptWb12IPI7TTQJT8Xobee1a7Tv3c7a9J/bNBDD9Xy','ShtvLN6Kmz9c2n5rDYkS73+WYn2NlHHP5mCAN4tVJM1HfF5ExaBwaBi3ax96KUGoU4Mld/IamEkj1M9Xxlm6krmCkZCZ7EW5CT/cAkd+hO23rgq3KvtEMQ4O+TclO2P45BIX6IkCKaSj02o6cK+hQ1tgCoCOC4YhYHFJJqC+fS9gm6USnNMY6cpibCWDJK/D','Machakos','','','','','','');
DROP TABLE IF EXISTS loan_appraisals;
CREATE TABLE `loan_appraisals` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `loan_no` text DEFAULT NULL,
  `action` text DEFAULT NULL,
  `action_by` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO loan_appraisals VALUES ('1','1','ShtvLN6Kmz9c2n5rDYkS79sOfTnx3CfE1ZD7uaCYE5qE7G/ne6RSbVi6Ir2NYneX/mpkKLhjqabwObFcM1b35VrjsyUhrsnRR1uZQxlV3+47OfOq6Y2ARQamM9BFNYrQ','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS775M6qGcFxTKMpyqhjf2qK3No0A2iO1QMENuVfWjX2cmPmbMsAbVWw3zNE39hjSmn9DSZcm2y9Qsrln8WsZ+jhShQTkTHMgb4YOJ4OjUsd98VIovJJnjk6pP0589SfbWhg==','ShtvLN6Kmz9c2n5rDYkS7wOrL49C2UQXp2gUgLOsGDN5ApS4YxhaCbK6kKn53N8s0moVmaid9o1YhKDCFZ6U3FfKlVDp6dfghAYVjsi0IRKzaejJyKFpM27PSgx5SgV7mCny/iDs5JjF6fEJGj28Gg==');
INSERT INTO loan_appraisals VALUES ('2','1','ShtvLN6Kmz9c2n5rDYkS749jqkb2GF22t59UxNK8rlztNowEr6vVAxPQ/svE9bRMRc6+08Py0QekkaOKZoMP2OlKgkhb31/0EDGGJYKX3i3KPz/Wr92rG0KMAen08nVu','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7w3rdAmrElriZAxreMvnHeDCpShp5+RXpQH9H8McLbAmvOpx+SxB21X4JNwzyE0N7sG6/4e70l0zQ1olr9V80Gz79mNkllLTLDe4X1wAfp7p','ShtvLN6Kmz9c2n5rDYkS7wF6tLe7rRgz2ttag2XKZINgGfGG5VUYSeg3pbNFVq3a4A6KYWaZDTiJ09bMBdg6Jg4i2sQSrhk1Uj5GuAAwLJ/NLWrwRoLS2kH3ocop/1ml0LOA2zVIL/GsT50nPtTItA==');
INSERT INTO loan_appraisals VALUES ('3','1','ShtvLN6Kmz9c2n5rDYkS72LzM2Ag+keG4F2NrdRv0gDyHA6wCVYOPK2xBjUpj8VW88yUi4k8J4cBSlV5dtbFXS2ptWb12IPI7TTQJT8Xobee1a7Tv3c7a9J/bNBDD9Xy','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS71pB/HOJzibA/wvXw77x0KVTpEM0AXCWi2WOi17Lqqb9ay8Wv+yNMQhU2FK+JJLXN2prOCaP28dXLR1omdr0CZ6bqlM3bXOF4ajGvH9AyIkI','ShtvLN6Kmz9c2n5rDYkS79yszrUsAqeQV/RkjFVSpcBEeN6Ai/7dJuREgsfbEOr2JrHI+n2o9coH4YPMdPSEU5T94mT498mPsEyasVcG6AaDCjKHsV9MY+isokvWp6uifRwtolIcJd4LRbJqrnBAuA==');
INSERT INTO loan_appraisals VALUES ('4','1','ShtvLN6Kmz9c2n5rDYkS78BcZbdMclqUM81rHHAdN3Vol9ozdBCchBarV4n3OvUUfx9GZhsLTlKSTGgEXZvcFPlVZuIdVtfly2c+D6QLdX6jUJJdSl/pbnBafJCmMC1l','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7/h0dlEyb1EY4Mw+rk9tLInAXbgfBKjjRjbpzH5+iahFBbleerrZFxfHl0KSclfnfvaUOHbYlmjz5Nqpaqdy+mv8UB77jbVs/MHLzZH7mq3jbSgheYe0G5ox8h8kJmslIPhNfX2BwA3j4nZlGKiyFlDW1gXYuAi3SE/Uy7u9/EH46RSJeuz9vWqA0TtH3Jav6PPNb+Zth+oASs7eTLUd/tE=','ShtvLN6Kmz9c2n5rDYkS7+21z4UF5aW3EICKn1rkYcB/KWgChcUoKcnQog7qe1LEzrtlwLMlxLE383taeNl2+sYTuZwAfoORWLNyF5DD7nr6nfqecujetXyFvQVZ/35hse1DSbIfIVjgULApPKN4CQ==');
INSERT INTO loan_appraisals VALUES ('5','2','ShtvLN6Kmz9c2n5rDYkS79sOfTnx3CfE1ZD7uaCYE5qE7G/ne6RSbVi6Ir2NYneX/mpkKLhjqabwObFcM1b35VrjsyUhrsnRR1uZQxlV3+47OfOq6Y2ARQamM9BFNYrQ','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS775M6qGcFxTKMpyqhjf2qK3No0A2iO1QMENuVfWjX2cmPmbMsAbVWw3zNE39hjSmn9DSZcm2y9Qsrln8WsZ+jhShQTkTHMgb4YOJ4OjUsd98VIovJJnjk6pP0589SfbWhg==','ShtvLN6Kmz9c2n5rDYkS78n6i4y9r8EHoo2uSyHmsRU5yrTabSzE0aenD/ygO9Hhgj0lxcsqIlf/1mh88DkGeIqQ8qA/Kt4DXnH3TUtl+HaPc+qytQHtGNjgy68ONTe5NG34XAaVDfP/sbZzLk8ivg==');
INSERT INTO loan_appraisals VALUES ('6','2','ShtvLN6Kmz9c2n5rDYkS749jqkb2GF22t59UxNK8rlztNowEr6vVAxPQ/svE9bRMRc6+08Py0QekkaOKZoMP2OlKgkhb31/0EDGGJYKX3i3KPz/Wr92rG0KMAen08nVu','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7w3rdAmrElriZAxreMvnHeDCpShp5+RXpQH9H8McLbAmvOpx+SxB21X4JNwzyE0N7sG6/4e70l0zQ1olr9V80Gz79mNkllLTLDe4X1wAfp7p','ShtvLN6Kmz9c2n5rDYkS70V0nc0BENVeVKgVpq90tR1k3Wug+FIlitfW40vql3Bth0CgmqLbD7QynaqXq/eqSQ3iBt/fbNY4aqMaA5BLdgyPc+qytQHtGNjgy68ONTe5g6YyxOTp6U++vcmp36nJ5g==');
INSERT INTO loan_appraisals VALUES ('7','2','ShtvLN6Kmz9c2n5rDYkS72LzM2Ag+keG4F2NrdRv0gDyHA6wCVYOPK2xBjUpj8VW88yUi4k8J4cBSlV5dtbFXS2ptWb12IPI7TTQJT8Xobee1a7Tv3c7a9J/bNBDD9Xy','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7w3rdAmrElriZAxreMvnHeDCpShp5+RXpQH9H8McLbAmvOpx+SxB21X4JNwzyE0N7sG6/4e70l0zQ1olr9V80Gz79mNkllLTLDe4X1wAfp7p','ShtvLN6Kmz9c2n5rDYkS77JDyjRtubwU7wLaSIr/r1Af/RUZyCNkE4M/Mju6p2/5LFweIA399eH8jRlAtXzArjvBGmhjAz/s9Xon9JfQXJiPc+qytQHtGNjgy68ONTe5J8lFm/vJbQQlzyczSo2+mw==');
INSERT INTO loan_appraisals VALUES ('8','3','ShtvLN6Kmz9c2n5rDYkS79sOfTnx3CfE1ZD7uaCYE5qE7G/ne6RSbVi6Ir2NYneX/mpkKLhjqabwObFcM1b35VrjsyUhrsnRR1uZQxlV3+47OfOq6Y2ARQamM9BFNYrQ','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS775M6qGcFxTKMpyqhjf2qK3No0A2iO1QMENuVfWjX2cmPmbMsAbVWw3zNE39hjSmn9DSZcm2y9Qsrln8WsZ+jhShQTkTHMgb4YOJ4OjUsd98VIovJJnjk6pP0589SfbWhg==','ShtvLN6Kmz9c2n5rDYkS70rWqntzKYOzKBEbCVOLYJ+sZf89qYZzM1u9qJQWvgUiNyX61QBJrpWOMlFqwl98KKwJSgOigk9db/098gOZIuEnK7dYjk0JJqpxnR7IzjLyJE8TWwX/jVQ2xxH0XSct7w==');
INSERT INTO loan_appraisals VALUES ('9','3','ShtvLN6Kmz9c2n5rDYkS749jqkb2GF22t59UxNK8rlztNowEr6vVAxPQ/svE9bRMRc6+08Py0QekkaOKZoMP2OlKgkhb31/0EDGGJYKX3i3KPz/Wr92rG0KMAen08nVu','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7w3rdAmrElriZAxreMvnHeDCpShp5+RXpQH9H8McLbAmvOpx+SxB21X4JNwzyE0N7sG6/4e70l0zQ1olr9V80Gz79mNkllLTLDe4X1wAfp7p','ShtvLN6Kmz9c2n5rDYkS72rH1RDQgj08T8RMMySWPiH0dXRdP4Hu9SfyYhsNe53+pI6kdm/cnRiGASG3buZiWYVoTRluf5UEs6Pku5n3yOtNPfICNZq+uBEWMjHI3w6I6erqTr0nvnxlaH3eS7THdg==');
INSERT INTO loan_appraisals VALUES ('10','3','ShtvLN6Kmz9c2n5rDYkS72LzM2Ag+keG4F2NrdRv0gDyHA6wCVYOPK2xBjUpj8VW88yUi4k8J4cBSlV5dtbFXS2ptWb12IPI7TTQJT8Xobee1a7Tv3c7a9J/bNBDD9Xy','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7w3rdAmrElriZAxreMvnHeDCpShp5+RXpQH9H8McLbAmvOpx+SxB21X4JNwzyE0N7sG6/4e70l0zQ1olr9V80Gz79mNkllLTLDe4X1wAfp7p','ShtvLN6Kmz9c2n5rDYkS7ytgaubSjWGgveq8JemUQivvtcJeBJ+VCYET/rtQLmIvHV43DtLPo4Cpd9jGxxwK0GILqqmqIiN43+rMkVPw7eJNPfICNZq+uBEWMjHI3w6IH/XkuO2wLRLqx4p86gLj0w==');
INSERT INTO loan_appraisals VALUES ('11','3','ShtvLN6Kmz9c2n5rDYkS78BcZbdMclqUM81rHHAdN3Vol9ozdBCchBarV4n3OvUUfx9GZhsLTlKSTGgEXZvcFPlVZuIdVtfly2c+D6QLdX6jUJJdSl/pbnBafJCmMC1l','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7zdr6VHKj1bixXE9Cxhn0Kk9Q2CrF8g9/yEqAO4RUT3UtNDgj8+vHtpwJbg9GVNNDrqq8mI6smHivYxMYE/b9VH8UB77jbVs/MHLzZH7mq3jDxub0pgVh1/cJtH2imAYThNTlD3rUUGOd23l3QVpXSviLpvZVVjdOyVimO99fAxl','ShtvLN6Kmz9c2n5rDYkS74tGglKj7dOYNX9fWkzDRqH9+R7IOsLlLsFpdR+mx3L1fjeLPkVLHFwP9sedhcXkAcWQI2u0yiMvu8SBCiFKUL2b9w5GHmOQDpNHK+F8JzxxctI9+MoVhU4NZjuTz4zebA==');
INSERT INTO loan_appraisals VALUES ('12','3','ShtvLN6Kmz9c2n5rDYkS78BcZbdMclqUM81rHHAdN3Vol9ozdBCchBarV4n3OvUUfx9GZhsLTlKSTGgEXZvcFPlVZuIdVtfly2c+D6QLdX6jUJJdSl/pbnBafJCmMC1l','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS74VQ/GkzltUc408WH/7fDuYnl7vtv2okygsLmL5KsJgFtoA1+0jPiu6gsB4LIwe+p2nmMrfg0GJul1y2+RdBsC78UB77jbVs/MHLzZH7mq3jKFZDZzIRIC1d+cQxvum7xgIry1jkv7PAYxbya/qo16bRqfZ0ql2FPUwHWf7NWcy7','ShtvLN6Kmz9c2n5rDYkS766wJ/d4NlLJzznasqNLrYpF3kRV0kROYxsSDWcv2aRPpGJIE3y2vK3QFaKDnWUl38e6LOWV2QJobKFRUDkVpNlcfhIeKxGkBMUjTdphtIP16aiAjbWcdYogVS3dtCdpfg==');
INSERT INTO loan_appraisals VALUES ('13','4','ShtvLN6Kmz9c2n5rDYkS79sOfTnx3CfE1ZD7uaCYE5qE7G/ne6RSbVi6Ir2NYneX/mpkKLhjqabwObFcM1b35VrjsyUhrsnRR1uZQxlV3+47OfOq6Y2ARQamM9BFNYrQ','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS775M6qGcFxTKMpyqhjf2qK3No0A2iO1QMENuVfWjX2cmPmbMsAbVWw3zNE39hjSmn9DSZcm2y9Qsrln8WsZ+jhShQTkTHMgb4YOJ4OjUsd98VIovJJnjk6pP0589SfbWhg==','ShtvLN6Kmz9c2n5rDYkS71Vj5lA9Jq0eb2o78wcSL+3DwSiK6ha5kiCHGAGw8r3t8zfTdXgZsJPRlnpEEHmXTkIjGQR+VSFmerTQqqUEa7r27Y7fWk+tJ+i3YOAJCW9Vg3vv8hA+79GZg/2FejO9Hw==');
INSERT INTO loan_appraisals VALUES ('14','4','ShtvLN6Kmz9c2n5rDYkS749jqkb2GF22t59UxNK8rlztNowEr6vVAxPQ/svE9bRMRc6+08Py0QekkaOKZoMP2OlKgkhb31/0EDGGJYKX3i3KPz/Wr92rG0KMAen08nVu','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7+T5i8No1LKbJevJqHQAQnqHtIyirAqKKt4I/DO9lJTNTn+Jrs1gdxdc3dGex6TRAimoiR881qMu/Gxlybtzcqb4H1T/NvP3I2LOn8S/L+RC','ShtvLN6Kmz9c2n5rDYkS7zBTKyFvyWE0llOwdmSVCoT2pmssH8+MNlMbqMp6oCCJAybV091fdrAO7CiCtC0VRbp5UxfWZEhloFS4oTKU52f27Y7fWk+tJ+i3YOAJCW9Vdss+Ixp9yIS4RX3SpaCobA==');
INSERT INTO loan_appraisals VALUES ('15','4','ShtvLN6Kmz9c2n5rDYkS72LzM2Ag+keG4F2NrdRv0gDyHA6wCVYOPK2xBjUpj8VW88yUi4k8J4cBSlV5dtbFXS2ptWb12IPI7TTQJT8Xobee1a7Tv3c7a9J/bNBDD9Xy','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7+T5i8No1LKbJevJqHQAQnqHtIyirAqKKt4I/DO9lJTNTn+Jrs1gdxdc3dGex6TRAimoiR881qMu/Gxlybtzcqb4H1T/NvP3I2LOn8S/L+RC','ShtvLN6Kmz9c2n5rDYkS70D5soVuRJYEmiwnaQvbzkS9KX/Q79jHwKdMXX1JAl+1ot+wEW1cGPfflrjgIE1WzYrEyF1EZ0ej5QBHfrm4cjr27Y7fWk+tJ+i3YOAJCW9VowAzNtQ46vUch7I3uuxkVQ==');
INSERT INTO loan_appraisals VALUES ('16','4','ShtvLN6Kmz9c2n5rDYkS78BcZbdMclqUM81rHHAdN3Vol9ozdBCchBarV4n3OvUUfx9GZhsLTlKSTGgEXZvcFPlVZuIdVtfly2c+D6QLdX6jUJJdSl/pbnBafJCmMC1l','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS709mAfFEBlFxtYWRUJR7SOh64RdkHUG4qWq9t06MIdB974xAMdDZu7y3WIgPiqMybWoFkkQY7C/v+lJnLqMRnif8UB77jbVs/MHLzZH7mq3jgliVQEpqNLN4gQdK0l4NwHfX6jTdAnKvht49L+IPpzeu8rEHBpLPnH51YacX7Drr','ShtvLN6Kmz9c2n5rDYkS7zR+9hUTSIsrzZPM1LXCETJmsh34VbRG3hGXQzZAReg1DaOzvsa35TU1vWmEjv+SROF3PgYJSo2e2r7lfFNQgYSeC2NXOLYx9BBmWtUIoXwNwFROHWlNdYNePtR5kDCvIg==');
DROP TABLE IF EXISTS loan_arrears;
CREATE TABLE `loan_arrears` (
  `s_no` text DEFAULT NULL,
  `loan_no` text DEFAULT NULL,
  `customer_no` mediumtext DEFAULT NULL,
  `customer_name` mediumtext DEFAULT NULL,
  `customer_phone` mediumtext DEFAULT NULL,
  `loan_product` mediumtext DEFAULT NULL,
  `loan_type` text DEFAULT NULL,
  `loan_amount` mediumtext DEFAULT NULL,
  `loan_term` mediumtext DEFAULT NULL,
  `no_of_installments` text DEFAULT NULL,
  `loan_interest` mediumtext DEFAULT NULL,
  `loan_installment` mediumtext DEFAULT NULL,
  `principalBal` text DEFAULT NULL,
  `interestBal` text DEFAULT NULL,
  `gross_loan` mediumtext DEFAULT NULL,
  `loan_balance` mediumtext DEFAULT NULL,
  `loan_applicationDate` mediumtext DEFAULT NULL,
  `take_home` mediumtext DEFAULT NULL,
  `loan_reviewer` mediumtext DEFAULT NULL,
  `loan_reviewDate` mediumtext DEFAULT NULL,
  `loan_approver` mediumtext DEFAULT NULL,
  `loan_approvalDate` mediumtext DEFAULT NULL,
  `loan_payments` mediumtext DEFAULT NULL,
  `firstRepaymentDate` text DEFAULT NULL,
  `repaymentFrequency` text DEFAULT NULL,
  `last_paymentDate` mediumtext DEFAULT NULL,
  `loan_status` mediumtext DEFAULT NULL,
  `loan_form` text DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  `loan_classification` mediumtext DEFAULT NULL,
  `days_inArrears` mediumtext DEFAULT NULL,
  `amount_inArrears` mediumtext DEFAULT NULL,
  `worst_classification` mediumtext DEFAULT NULL,
  `worst_daysInArrears` mediumtext DEFAULT NULL,
  `loan_writeoff` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO loan_arrears VALUES ('4','4','1','ShtvLN6Kmz9c2n5rDYkS74OtBJLWrMJ0g/d09kNlMHa4BQVl5Zh0nDVCWjEXBYkIzN7K05ROctAdvAkfE6n9xPJ2sTsSUNnE9FNskVM6c5QbJxnNmg939PW2dD8/wF2I','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS77oPmOGxuU4c6wSOPXw9ZxQ77eSoGQfh0W2McsG4lzrEjHM4DX5RxjNXBu/gZdj9aWHlJApove9qrQ0gkGeW0Rf0LaubMQEIHdLf6VRLU4PP','ShtvLN6Kmz9c2n5rDYkS715mOMyO+MP5n+QGGDuqypvO1XBuGIcA0NKmBO3wtaQNxH8lM/Pd85xME6yuVGb5TnVWRlTPhpfVSOgarH9YaYdyirEwLQHG20QA0Nx/3p1E','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS74F/t0sPLXYaPWaPmiaEu2bH2PaA1yS7gJPixbhtG4mGVaiJlihEGuo33EI+nVhfBala4oHwvv/t/quuNPs6egYEbM28O7JDyKzrMqcSdcNV','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS78As+glD9pxV4PCKmgvrTGuP4cWX+NR6euMaPQQWmIkqdZrsGxMaIu2s7FqPyhfENJQD8g4xnEmQZeU/RIT0rQzmVxvUc8B5RWVkM1FpkY39uqTkT68ntO8p6CXjAq4EmAU9WLmjZZaQlj5R1k3jTiAf+KDRCRFpcpcV1VpIETvdRmTHosFRkewp7RC5An7xPWyrpi5t6SvkEQ70AqJBYa67iWRWH6BOZs22U1AqfwzwOHU+RhxA2v2ebGxSSfnWBz2dJUZpwnON5l8hcqBu8LQ=','','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS70br0l9+Qdctd2P21FgboYkYZOnu0K4oUSVu1Va3wj8sv7ntK8lgP6+LozW6yWtKqEsuEb+avBCHyDYQQmWmkixUhjmNbW9Oqf2NRqF9l9XO','ShtvLN6Kmz9c2n5rDYkS71Vj5lA9Jq0eb2o78wcSL+3DwSiK6ha5kiCHGAGw8r3t8zfTdXgZsJPRlnpEEHmXTkIjGQR+VSFmerTQqqUEa7r27Y7fWk+tJ+i3YOAJCW9Vg3vv8hA+79GZg/2FejO9Hw==','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7zBTKyFvyWE0llOwdmSVCoT2pmssH8+MNlMbqMp6oCCJAybV091fdrAO7CiCtC0VRbp5UxfWZEhloFS4oTKU52f27Y7fWk+tJ+i3YOAJCW9Vdss+Ixp9yIS4RX3SpaCobA==','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS70D5soVuRJYEmiwnaQvbzkS9KX/Q79jHwKdMXX1JAl+1ot+wEW1cGPfflrjgIE1WzYrEyF1EZ0ej5QBHfrm4cjr27Y7fWk+tJ+i3YOAJCW9VowAzNtQ46vUch7I3uuxkVQ==','ShtvLN6Kmz9c2n5rDYkS73UdnVktc8fzYWAPbOHuA3CbkXnoBEcp3rUN9w4FLhQ6COUeHAgr5uezDkXznUZeNQ4H4QUFfQlzxsPz7IAGZ5jRFYw4navOFGY8ItjIB114','ShtvLN6Kmz9c2n5rDYkS78Ispkh8pVWjlVgxJVYJNM1s1Qlxp5fwlR8jFEKhdxyRsBqIOeYxGi0FcbA6k75Dq5rwlWFTTf48OtbJBIUiOkv9Z2eESDV66OtmUDCBBzpn','ShtvLN6Kmz9c2n5rDYkS77Vxs+7JnSqh7gdTdvExya8RVOO0JQ3Ye0k1kLcgo8ripq82Mur9HM5AWPKnlw6qznRbIMXFO6nD231fWFpCcwQB/F+vceKTAkTZbZEGkCFR','ShtvLN6Kmz9c2n5rDYkS78YDBt2NaX/BSOIHJTxJ8vKYplPMKAvWDCVNYMYbems4/TMX8LUdEucP4+7XhziQic6H20k8WkzQ1vlbY9qq5cYqX9B4i2V6DGwGwxtkGJYI','ShtvLN6Kmz9c2n5rDYkS78m9ScrTA1pBXtG+Ix2Z7xtW/DWnxG/7uvJH4suIZxZQihJwNwZkTXPCQUGxljp1UDX3GoTCIBvOmtGNaJIWR/sfkRxobkcS86k1LON+0VvE','ShtvLN6Kmz9c2n5rDYkS73+WYn2NlHHP5mCAN4tVJM1HfF5ExaBwaBi3ax96KUGoU4Mld/IamEkj1M9Xxlm6krmCkZCZ7EW5CT/cAkd+hO23rgq3KvtEMQ4O+TclO2P45BIX6IkCKaSj02o6cK+hQ1tgCoCOC4YhYHFJJqC+fS9gm6USnNMY6cpibCWDJK/D','Machakos','ShtvLN6Kmz9c2n5rDYkS78B/MoCmPnaz+borursp8PvUD4L2Jdfs1g3ic7i8HGafX63C/RE96ZUY6anrKsVffZFyKcG/OfDsmZDdQ7sRqluqT5ioQRMYOfcqxlePm3kj','ShtvLN6Kmz9c2n5rDYkS772TBufWQTlGonpD3tyKu8zZCC5ZjlO9aVl4eKjuiRzF9YAMD39dPTcFQ5dh5duJFAQcGE14++Ds2mA9HgUlM7/HKKE4WdgrFkR0L/8GEHek','ShtvLN6Kmz9c2n5rDYkS72dWFC5uwvy+7GFMTEzEcLG7sMJqmHAhulod56KD+0TICTJsPJ3Iqxw0lz8rnlDrqnuzwprbfltBSMEJdf6OWjvW+WeoLGExra1It8nzp4nj','ShtvLN6Kmz9c2n5rDYkS7zqbD2rDWnu0d0FQ++/EfPl9etFJAKcNdfNop57ch90rH1gx0n13ZzhOVu7jdh8bramxSi4hCEzpzyyt7YSSahwcbjbF7rq3+tb9ZB05Neg3','ShtvLN6Kmz9c2n5rDYkS7/lsvjhkzkE9qDmpSC20Q+BWZ6wK7l2yKqQQnVDMsPcWz0rMHpF7FExFPZKcBFNF3PQ2s8G231mAjnwLeZ7eQJZy/UinPpZPX8y9jQ8O50Bo','');
DROP TABLE IF EXISTS loan_classification;
CREATE TABLE `loan_classification` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `product_no` mediumtext DEFAULT NULL,
  `product_name` mediumtext DEFAULT NULL,
  `days_inArrears` mediumtext DEFAULT NULL,
  `classification` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS loan_products;
CREATE TABLE `loan_products` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `product_no` mediumtext DEFAULT NULL,
  `product_name` mediumtext DEFAULT NULL,
  `product_maxAmount` mediumtext DEFAULT NULL,
  `product_maxTerm` mediumtext DEFAULT NULL,
  `product_interest` mediumtext DEFAULT NULL,
  `product_fees` mediumtext DEFAULT NULL,
  `repaymentFrequency` text DEFAULT NULL,
  `product_status` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO loan_products VALUES ('1','1','ShtvLN6Kmz9c2n5rDYkS77oPmOGxuU4c6wSOPXw9ZxQ77eSoGQfh0W2McsG4lzrEjHM4DX5RxjNXBu/gZdj9aWHlJApove9qrQ0gkGeW0Rf0LaubMQEIHdLf6VRLU4PP','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS74F/t0sPLXYaPWaPmiaEu2bH2PaA1yS7gJPixbhtG4mGVaiJlihEGuo33EI+nVhfBala4oHwvv/t/quuNPs6egYEbM28O7JDyKzrMqcSdcNV','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv','ShtvLN6Kmz9c2n5rDYkS77Vxs+7JnSqh7gdTdvExya8RVOO0JQ3Ye0k1kLcgo8ripq82Mur9HM5AWPKnlw6qznRbIMXFO6nD231fWFpCcwQB/F+vceKTAkTZbZEGkCFR','ShtvLN6Kmz9c2n5rDYkS79pgqR5FQ068SKGSN8Qt4d+nuvykanvidfH/7v0wZXe37j3UIR+9wubJFPoICaLJ8j2MVVvvVUBDIyTlGCuywSAqynCd3RnR8Imhp2/Q+03o');
DROP TABLE IF EXISTS loan_schedules;
CREATE TABLE `loan_schedules` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `loan_no` text DEFAULT NULL,
  `customer_no` text DEFAULT NULL,
  `customer_phone` text DEFAULT NULL,
  `loan_amount` text DEFAULT NULL,
  `principal` text DEFAULT NULL,
  `interest` text DEFAULT NULL,
  `loan_installment` text DEFAULT NULL,
  `due_date` text DEFAULT NULL,
  `paid` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO loan_schedules VALUES ('1','1','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS71Z8dYIK75mwdp4LZKyX5ISPZxnoC8TLMBa7eQtXO8gqTtZCDJaI8+hJgtjmgEvgi3JBz/85OW3kXL85ImCxToSRLYU5UVGy+NMMJNQtWXNq','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('2','1','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS7y0+Yq+SJTlV8ijt21lKqSsaJ6SI8fOydvtQBUkKXlhoUGB/aEzaKS4unr0dfFwnSR8g46EsneLUVVzRj7gSxKFUdcHr4aBIuLImGoqo7Yuh','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('3','1','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS77ijbvV4i19eLJG9aCHMIBiPh3eJtSGt/dXqBAo/VtJZnDTd1JsPdxDmuEShu8y6XF+rThL5HjoYjMR6Q95s2FDNLR3OcOtHuSzFNNIwdOo5','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('4','1','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS7ySFJGIVFrGLlJcHvEzw/TiE/QdixGEzlot1QYZF21D3bbIHHGZY8Qk94v7gqMnZtKwYMXDK2f072HGQjnBCAqhDVmTAaG7bENgDB3kHRksX','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('5','1','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS736B/3Ak05skKtroYWxwOlmJBLBBVkJJXg2bQIay02U0dezLBeI5rvkFMzN0yAZnPahdb+0iJGx09txsdjnQy3PjYN2uWzi9xJJmMN6FHihg','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('6','1','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS71EeI7Ya3BXWpE0p7X1B+jj0J3Fu19p8ETujdANWNx0cJP4pmNpWcIl5uV16oaEXqHn2yahyfk2s23HGCTjReXx/R4CRI4ZRj5ia1xs3dlyT','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv','ShtvLN6Kmz9c2n5rDYkS71EeI7Ya3BXWpE0p7X1B+jj0J3Fu19p8ETujdANWNx0cJP4pmNpWcIl5uV16oaEXqHn2yahyfk2s23HGCTjReXx/R4CRI4ZRj5ia1xs3dlyT','ShtvLN6Kmz9c2n5rDYkS71EeI7Ya3BXWpE0p7X1B+jj0J3Fu19p8ETujdANWNx0cJP4pmNpWcIl5uV16oaEXqHn2yahyfk2s23HGCTjReXx/R4CRI4ZRj5ia1xs3dlyT','ShtvLN6Kmz9c2n5rDYkS74VIq+iPf5DyduqYjxf1ved5XdOLvdhQ0Xrx1ypKEqGqUdsm8WcOS+RN7aDfelcpE1E7r/4Xz4OqfzGXgGwMr3e7ZI5dax3U43WlN3Y0Nv6x','ShtvLN6Kmz9c2n5rDYkS71EeI7Ya3BXWpE0p7X1B+jj0J3Fu19p8ETujdANWNx0cJP4pmNpWcIl5uV16oaEXqHn2yahyfk2s23HGCTjReXx/R4CRI4ZRj5ia1xs3dlyT');
INSERT INTO loan_schedules VALUES ('7','2','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('8','2','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS7zJieXLvydsaq4XbYoXqTmV/yl3VAimtVfQCgOmaA8rKJKgqCG418/awNyfBr9x0icgjo8/llnLdpGXASHnd6rZMLO0XdR/vg0rkpZ2caCzl','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('9','2','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS7006gt5nkl5cA9t/+0gR3tdmvi1qSJ3TAjjRc5ghATX5lNdqu1CvH0JXPIX5TeNegXGpwdwxYsi/3mjAF3FSTihB1xXZg4cI88UkCTikqDz1','ShtvLN6Kmz9c2n5rDYkS73NGm8REtpL2Gojr12uzkLlemWJccG1ndk6o2kTO/SptcAjDGXoMDnv1FCP3xSr4E+8Bg1W7mHpVHZWMnEblkdrBuDbMjSyik+KQDDj58QGZ');
INSERT INTO loan_schedules VALUES ('10','2','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS758uV8ioNoFwjkMPd/Q/D+GVsOOI6ueymdvXrk9/88RT94HDtb3a5TiM2UaPjuRvPjCpYhsoRHknMQPXd+aIdoZTQzwbRIvJKzY2SwnCR5VM','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('11','2','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS7xZjG3awPou8Svvv0/jy6vCpHs8kgpFyN8pANC3jrDXwrfOKlomLMLn9gSH0VEq7YUdDf/NBpDpxOLgC7Be0Uf+N6yBNou9jxVv0y2zucHyQ','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('12','3','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('13','3','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS7zJieXLvydsaq4XbYoXqTmV/yl3VAimtVfQCgOmaA8rKJKgqCG418/awNyfBr9x0icgjo8/llnLdpGXASHnd6rZMLO0XdR/vg0rkpZ2caCzl','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('14','3','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS7006gt5nkl5cA9t/+0gR3tdmvi1qSJ3TAjjRc5ghATX5lNdqu1CvH0JXPIX5TeNegXGpwdwxYsi/3mjAF3FSTihB1xXZg4cI88UkCTikqDz1','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('15','3','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS758uV8ioNoFwjkMPd/Q/D+GVsOOI6ueymdvXrk9/88RT94HDtb3a5TiM2UaPjuRvPjCpYhsoRHknMQPXd+aIdoZTQzwbRIvJKzY2SwnCR5VM','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('16','3','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS7xZjG3awPou8Svvv0/jy6vCpHs8kgpFyN8pANC3jrDXwrfOKlomLMLn9gSH0VEq7YUdDf/NBpDpxOLgC7Be0Uf+N6yBNou9jxVv0y2zucHyQ','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('17','3','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS7ybHNLQlta0x29XhlcXi5OQsNO04LutHNNML94nse8C5fnbyVVI6XmtZcGlogbEjslF25SbVcPu003ZyQVPxtHNCNBhi1+YWx9VoT7kW3sYa','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv','ShtvLN6Kmz9c2n5rDYkS7ybHNLQlta0x29XhlcXi5OQsNO04LutHNNML94nse8C5fnbyVVI6XmtZcGlogbEjslF25SbVcPu003ZyQVPxtHNCNBhi1+YWx9VoT7kW3sYa','ShtvLN6Kmz9c2n5rDYkS7ybHNLQlta0x29XhlcXi5OQsNO04LutHNNML94nse8C5fnbyVVI6XmtZcGlogbEjslF25SbVcPu003ZyQVPxtHNCNBhi1+YWx9VoT7kW3sYa','ShtvLN6Kmz9c2n5rDYkS72ZDiFt5JJCXBExB9NI63aMFoOYE5xi+ePO/N/eH+fsATLkPgbiLD337dCbVQ9um76t0znwBIpuBlreCMhhY2I74FdWBRm3kSyzXscFpnitE','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('18','3','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS79CvRDVVkZeHhW3f/iNC9kC2tJgpz52u+pA4QU5xDoJSLoIyKA+bMmgDn+4u265tWt9DhtnmbBRo8t18ppwLC39C0p4RoWrID6DLatqs1h0B','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv','ShtvLN6Kmz9c2n5rDYkS79CvRDVVkZeHhW3f/iNC9kC2tJgpz52u+pA4QU5xDoJSLoIyKA+bMmgDn+4u265tWt9DhtnmbBRo8t18ppwLC39C0p4RoWrID6DLatqs1h0B','ShtvLN6Kmz9c2n5rDYkS79CvRDVVkZeHhW3f/iNC9kC2tJgpz52u+pA4QU5xDoJSLoIyKA+bMmgDn+4u265tWt9DhtnmbBRo8t18ppwLC39C0p4RoWrID6DLatqs1h0B','ShtvLN6Kmz9c2n5rDYkS7+yGwzIgFEUm0qKMW7WLA8eH8M9pZmhfqlyBHRdOLMKTJDgvb1mmD+ZCECD3Zr1fxXqd7fYw9ySqC6D69mXtR9TxLNm0+wiJSHEAmjXF7t7B','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('19','4','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS78Ispkh8pVWjlVgxJVYJNM1s1Qlxp5fwlR8jFEKhdxyRsBqIOeYxGi0FcbA6k75Dq5rwlWFTTf48OtbJBIUiOkv9Z2eESDV66OtmUDCBBzpn','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc');
INSERT INTO loan_schedules VALUES ('20','4','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS7/dGFx9FFQzEZxGVwlTHJ37fFWT4vbIKdeLbAIpeM3bsv/guESIc5RxYBdPbEGo6Ir7IxYfcXvAgRvQqKMxpTLZmPSi7NqNG8brNYgAVdyYA','ShtvLN6Kmz9c2n5rDYkS7zY7i1bVbMLi+ftLdxZIjmDLOIx+EGf4RpNE6rAqRUfb9M/zSm34BRnQm4ptPsSQXGDlmT0c3OvNyAwmpvKPKXsvK8i6BjjafsHJehWAubVs');
INSERT INTO loan_schedules VALUES ('21','4','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS71Z8dYIK75mwdp4LZKyX5ISPZxnoC8TLMBa7eQtXO8gqTtZCDJaI8+hJgtjmgEvgi3JBz/85OW3kXL85ImCxToSRLYU5UVGy+NMMJNQtWXNq','');
INSERT INTO loan_schedules VALUES ('22','4','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS7y0+Yq+SJTlV8ijt21lKqSsaJ6SI8fOydvtQBUkKXlhoUGB/aEzaKS4unr0dfFwnSR8g46EsneLUVVzRj7gSxKFUdcHr4aBIuLImGoqo7Yuh','');
INSERT INTO loan_schedules VALUES ('23','4','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS73pvL/U2pwiLUmSmNXxPS+lSlP0gxtvGSNr3FJXeSCsdafwsYtGRU8ETgqmKcZfeuEbI66GUcFURHrz/+P5L5jb55j5O6LOcBKl/LTycO2YU','ShtvLN6Kmz9c2n5rDYkS712ot/n/HCAgQtJWtY3LwFKzjHFuTEZhfTGlXkr+CB3mGjr7GuW4fSzwBOpX5zi4GUiVjARQB6a5b/5CQRjZ/Kr8rFeO1Y9ccjqpbExvdioB','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS77ijbvV4i19eLJG9aCHMIBiPh3eJtSGt/dXqBAo/VtJZnDTd1JsPdxDmuEShu8y6XF+rThL5HjoYjMR6Q95s2FDNLR3OcOtHuSzFNNIwdOo5','');
INSERT INTO loan_schedules VALUES ('24','4','1','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS78J6bH6BUaGWkl1/1qyvyQGUQO3Uw1ekf1wQTjSUXqjBkOmEmKHOOT29xDmJO7qheM/ekC2Ppm1WgXcAUbwTGSNAIltAoE2C+q0NeqWLHEnN','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv','ShtvLN6Kmz9c2n5rDYkS78J6bH6BUaGWkl1/1qyvyQGUQO3Uw1ekf1wQTjSUXqjBkOmEmKHOOT29xDmJO7qheM/ekC2Ppm1WgXcAUbwTGSNAIltAoE2C+q0NeqWLHEnN','ShtvLN6Kmz9c2n5rDYkS78J6bH6BUaGWkl1/1qyvyQGUQO3Uw1ekf1wQTjSUXqjBkOmEmKHOOT29xDmJO7qheM/ekC2Ppm1WgXcAUbwTGSNAIltAoE2C+q0NeqWLHEnN','ShtvLN6Kmz9c2n5rDYkS7ySFJGIVFrGLlJcHvEzw/TiE/QdixGEzlot1QYZF21D3bbIHHGZY8Qk94v7gqMnZtKwYMXDK2f072HGQjnBCAqhDVmTAaG7bENgDB3kHRksX','');
DROP TABLE IF EXISTS loan_transactions;
CREATE TABLE `loan_transactions` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `loan_no` mediumtext DEFAULT NULL,
  `customer_no` mediumtext DEFAULT NULL,
  `posting_date` mediumtext DEFAULT NULL,
  `posting_description` mediumtext DEFAULT NULL,
  `description_no` mediumtext DEFAULT NULL,
  `debit` mediumtext DEFAULT NULL,
  `credit` mediumtext DEFAULT NULL,
  `payment_mode` mediumtext DEFAULT NULL,
  `transaction_by` mediumtext DEFAULT NULL,
  `running_balance` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO loan_transactions VALUES ('1','1','1','ShtvLN6Kmz9c2n5rDYkS79yszrUsAqeQV/RkjFVSpcBEeN6Ai/7dJuREgsfbEOr2JrHI+n2o9coH4YPMdPSEU5T94mT498mPsEyasVcG6AaDCjKHsV9MY+isokvWp6uifRwtolIcJd4LRbJqrnBAuA==','ShtvLN6Kmz9c2n5rDYkS715mOMyO+MP5n+QGGDuqypvO1XBuGIcA0NKmBO3wtaQNxH8lM/Pd85xME6yuVGb5TnVWRlTPhpfVSOgarH9YaYdyirEwLQHG20QA0Nx/3p1E','ShtvLN6Kmz9c2n5rDYkS75AMFv24oQYysQEOCyxR/x9uezno1h6ncCn9rf4W7wHIb2UiBz4dHILWXKyFl5HP+GAjuE0Lj6TkJzMOmvzNDif7hwdk5O0xbHoJR0JVJDkt','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','','','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH');
INSERT INTO loan_transactions VALUES ('2','1','1','ShtvLN6Kmz9c2n5rDYkS71Z8dYIK75mwdp4LZKyX5ISPZxnoC8TLMBa7eQtXO8gqTtZCDJaI8+hJgtjmgEvgi3JBz/85OW3kXL85ImCxToSRLYU5UVGy+NMMJNQtWXNq','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS73o3OTNwS8LxDacgvhw8wiMezxO91TE3JPARySKWW1yiLm7xHpwOvniwcJbY7CaVmVtWSn+1uCfSwu2OT55ot/4glQQqhAXeHuNgdR15sq7Q','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS74Neo0cdkNI/1BMkCYB+Gj9HYiexYlLFRMwCPiTWGMtb+clUYhYHHcRuxoHLqc/99v6M2Y92EdnxkL1CLMpoCi0QKZAM2SbINwBvG+iz8fqx');
INSERT INTO loan_transactions VALUES ('3','1','1','ShtvLN6Kmz9c2n5rDYkS71Z8dYIK75mwdp4LZKyX5ISPZxnoC8TLMBa7eQtXO8gqTtZCDJaI8+hJgtjmgEvgi3JBz/85OW3kXL85ImCxToSRLYU5UVGy+NMMJNQtWXNq','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS71EeI7Ya3BXWpE0p7X1B+jj0J3Fu19p8ETujdANWNx0cJP4pmNpWcIl5uV16oaEXqHn2yahyfk2s23HGCTjReXx/R4CRI4ZRj5ia1xs3dlyT','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS71AW7LihKw4bgfurjFahMGc1py99hBNDYgk/0ZUeP8RTQpCaqdPNLJbdSA6DtbygjWpCRfzbmY/8U7Bzpqt6TlkbgbTZTw/GIYsWXZr0qli7');
INSERT INTO loan_transactions VALUES ('4','1','1','ShtvLN6Kmz9c2n5rDYkS7+21z4UF5aW3EICKn1rkYcB/KWgChcUoKcnQog7qe1LEzrtlwLMlxLE383taeNl2+sYTuZwAfoORWLNyF5DD7nr6nfqecujetXyFvQVZ/35hse1DSbIfIVjgULApPKN4CQ==','ShtvLN6Kmz9c2n5rDYkS76pgPrqUe55eyldI+Kf+spni/uDSgTUoZdy61eq4RIlBzzGcNCrCQKzx51sQ05p2wOHwFE+GzpZIrBCol8VOho3emG0xgaSskE/lzfXzuEu5','ShtvLN6Kmz9c2n5rDYkS75AMFv24oQYysQEOCyxR/x9uezno1h6ncCn9rf4W7wHIb2UiBz4dHILWXKyFl5HP+GAjuE0Lj6TkJzMOmvzNDif7hwdk5O0xbHoJR0JVJDkt','ShtvLN6Kmz9c2n5rDYkS71EeI7Ya3BXWpE0p7X1B+jj0J3Fu19p8ETujdANWNx0cJP4pmNpWcIl5uV16oaEXqHn2yahyfk2s23HGCTjReXx/R4CRI4ZRj5ia1xs3dlyT','','ShtvLN6Kmz9c2n5rDYkS76pgPrqUe55eyldI+Kf+spni/uDSgTUoZdy61eq4RIlBzzGcNCrCQKzx51sQ05p2wOHwFE+GzpZIrBCol8VOho3emG0xgaSskE/lzfXzuEu5','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS74Neo0cdkNI/1BMkCYB+Gj9HYiexYlLFRMwCPiTWGMtb+clUYhYHHcRuxoHLqc/99v6M2Y92EdnxkL1CLMpoCi0QKZAM2SbINwBvG+iz8fqx');
INSERT INTO loan_transactions VALUES ('5','1','1','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS74Neo0cdkNI/1BMkCYB+Gj9HYiexYlLFRMwCPiTWGMtb+clUYhYHHcRuxoHLqc/99v6M2Y92EdnxkL1CLMpoCi0QKZAM2SbINwBvG+iz8fqx','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv');
INSERT INTO loan_transactions VALUES ('6','2','1','ShtvLN6Kmz9c2n5rDYkS77JDyjRtubwU7wLaSIr/r1Af/RUZyCNkE4M/Mju6p2/5LFweIA399eH8jRlAtXzArjvBGmhjAz/s9Xon9JfQXJiPc+qytQHtGNjgy68ONTe5J8lFm/vJbQQlzyczSo2+mw==','ShtvLN6Kmz9c2n5rDYkS715mOMyO+MP5n+QGGDuqypvO1XBuGIcA0NKmBO3wtaQNxH8lM/Pd85xME6yuVGb5TnVWRlTPhpfVSOgarH9YaYdyirEwLQHG20QA0Nx/3p1E','ShtvLN6Kmz9c2n5rDYkS7+gdpYVZeuR6/Hc3vrIsT54/RjjaF/LWKJjR3ONdUbyw4t2AILbthilO4B1ZXEG1bcyqJxOhX9e4eAU0XvbNqjjKNIvewdjgt+PlvAIAPUas','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','','','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH');
INSERT INTO loan_transactions VALUES ('7','2','1','ShtvLN6Kmz9c2n5rDYkS71Z8dYIK75mwdp4LZKyX5ISPZxnoC8TLMBa7eQtXO8gqTtZCDJaI8+hJgtjmgEvgi3JBz/85OW3kXL85ImCxToSRLYU5UVGy+NMMJNQtWXNq','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS74bKeba8n8jizgkm+vTlq9cxlVJemU6YJ46LzNH8zuDrS0TUSdIWTgtNo92bBeLetJPXu2og1y5Kcd3ARLuRbnZPmGqem/gRE4b91ic4FTon','','ShtvLN6Kmz9c2n5rDYkS7zFmlOG8ddhU0JMytnvJVJ9j3GFepl+kGR0Zp4P8gjDX2sAP9oQQUkp7uCrjkQvjPowN1EiZreCegiKJEu0HAx0hRSJflVTzDIFeewQ1IfF+','ShtvLN6Kmz9c2n5rDYkS74bKeba8n8jizgkm+vTlq9cxlVJemU6YJ46LzNH8zuDrS0TUSdIWTgtNo92bBeLetJPXu2og1y5Kcd3ARLuRbnZPmGqem/gRE4b91ic4FTon','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS79dh3C5hviRMqHNflMHGHZxUMviRzZCav92FA9N4XqeK/3G91S+pyk7FTjhDDuib07q22kRiT+RXHZGzVUwWeLVOWU1fkrj4nWuF21q0DDnf');
INSERT INTO loan_transactions VALUES ('8','2','1','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS79dh3C5hviRMqHNflMHGHZxUMviRzZCav92FA9N4XqeK/3G91S+pyk7FTjhDDuib07q22kRiT+RXHZGzVUwWeLVOWU1fkrj4nWuF21q0DDnf');
INSERT INTO loan_transactions VALUES ('9','3','1','ShtvLN6Kmz9c2n5rDYkS7ytgaubSjWGgveq8JemUQivvtcJeBJ+VCYET/rtQLmIvHV43DtLPo4Cpd9jGxxwK0GILqqmqIiN43+rMkVPw7eJNPfICNZq+uBEWMjHI3w6IH/XkuO2wLRLqx4p86gLj0w==','ShtvLN6Kmz9c2n5rDYkS7wQPSwJBxdAP9JoFEmroh60jSIIa3E3ucB300xW6LEoxumqiCSRFb8Q0xgbCyOEtJ2tHEez0qJIopubkqPypLKkiKO+reXmG+G5fkukBLShG','ShtvLN6Kmz9c2n5rDYkS7zBu8AxBr4pVTDwdlLNQGxw3J2oNjBfoKuqm8RRtDVDvmm4/Izk9PrFiDLPhPOi71m71FXuqM9pG68ZOWZnBIkp05jyu7G1n0NiUmVdjOUcl','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','','','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH');
INSERT INTO loan_transactions VALUES ('10','2','1','ShtvLN6Kmz9c2n5rDYkS7ytgaubSjWGgveq8JemUQivvtcJeBJ+VCYET/rtQLmIvHV43DtLPo4Cpd9jGxxwK0GILqqmqIiN43+rMkVPw7eJNPfICNZq+uBEWMjHI3w6IH/XkuO2wLRLqx4p86gLj0w==','ShtvLN6Kmz9c2n5rDYkS7zehVdVtLRgYAKg7yPN6zwFYVm2bWmLJ6XFqFPbXOKQAiKT3Q4q9TtS5drMKOSitqzl2IaHkgFN/fVx7BJGVpp+6AF7TjrkoViX8rw/rOlWQ','ShtvLN6Kmz9c2n5rDYkS7+gdpYVZeuR6/Hc3vrIsT54/RjjaF/LWKJjR3ONdUbyw4t2AILbthilO4B1ZXEG1bcyqJxOhX9e4eAU0XvbNqjjKNIvewdjgt+PlvAIAPUas','','ShtvLN6Kmz9c2n5rDYkS79dh3C5hviRMqHNflMHGHZxUMviRzZCav92FA9N4XqeK/3G91S+pyk7FTjhDDuib07q22kRiT+RXHZGzVUwWeLVOWU1fkrj4nWuF21q0DDnf','','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','');
INSERT INTO loan_transactions VALUES ('11','3','1','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS7+K2cqy8QjO/okqE8lglyiW0P89byOQmqr/lcU1+4b/OuF59CA2V+VY+cF8Pd6B50LVxuFG8EWp5DO27Wlid/lNZrQXUkwfG9EiP9rP3uouK','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS75O6Wc/lvfh6MIqw1npWHcn5ywRjNaQMsC9ccfLQ2VxhW2lD5GeM0x5b1+vQ/ZJxT0x9NuUKAG9epv7B+4eZoPdECOMbNIkAllbPZDdVpYJ4');
INSERT INTO loan_transactions VALUES ('12','3','1','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS72rZNCx9ZTJik3tAZ/C7sBNp7cMzFwRuCJPTGTzUqdAyAqceSQs74JE2610Hd72cNANJCP8kjQWrOk9DSVoUHbAqVImMHEgc7snX8SS/EzP3','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS78k/dUuBFiog4FzEP7C0toOP34MzK3uhOcUy9hjn1IJB4mNZwLu4JgwZkBU96c+vzpKVDPqJUg7RF9Bbn6QYWk6Aoi+QAHgFfjRo3pWOteCk');
INSERT INTO loan_transactions VALUES ('13','3','1','ShtvLN6Kmz9c2n5rDYkS70X4BAOTHDpASxL4N4fVPk2b17PYUTmAx4N6SBkm0HgD4LP1gU7fSBQxEuQF4EifpXIMpYMpOYLF+8XvOB51AWEhKocss2HQRAilUBGFInn5','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS7+K2cqy8QjO/okqE8lglyiW0P89byOQmqr/lcU1+4b/OuF59CA2V+VY+cF8Pd6B50LVxuFG8EWp5DO27Wlid/lNZrQXUkwfG9EiP9rP3uouK','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS74SRGn7WNUg0mXV2izgViO7lHnVo38XxmPg7KDrwt9ZIaiQtOwVuAnOhGZwUzs2Ypp2dV6rrhkSK0tvN5/E+mmeH3BXm5aOfBe1y2KxhyHoL');
INSERT INTO loan_transactions VALUES ('14','3','1','ShtvLN6Kmz9c2n5rDYkS7+egZkW9p85EmtJJ4CwoiALLNP4ejVKVbkzflBRizv8g71OVshbsBzjLVohqv97lhQBJC4zCF8/vh60+sqyWxoEvJ8F0xPl+dRUAf4cCMlPT','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS759ZeA+RVAnALyLz1x/ea3rWmv/7unql56FyycwR8VqjshrMHOIIwHUGkYoMXXtu6v3xAU+M3j4VH9LSCAwa0Yr5t6uZ61P/YaQSf6pLDMUV','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS7950dEPzE7lqrd8hXF1eT7clDRJeiA8sWcvwUUwgLdgoKkuXBjgTpSF0SMYSR1pGIzUtqbig3HZsBiljzgeGgAj/AXlwsQ0B3I4CAJK5Y8Me');
INSERT INTO loan_transactions VALUES ('15','3','1','ShtvLN6Kmz9c2n5rDYkS70JEPB5Ct4Av9XBlvGBnTOqjqKmUFrBSu3CKXtfv57Z71aclAdR14q6wGmZzn40TFCo0SIF+4PzTqlHsErmIhIJjDVkKL4uZmZurSLTXcaUK','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS7zV/9zezqWUty90/Gmqcme84/dJkuXkACp8nCwdsCN8az8RPJeR7xQ/n9bwcaq4hL+NsVz4RnQav6TLnh3/Yhau/CsXDLo67+radvB/6U5Mu','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS72932MMz3neKvcCuxVwSSSOSeJ+akozGDCC5XdfW8UxGdjAvEwGQ70YQAGv6ezq0oQ9mvxzprl1RDxgLr2ZgIcGRy5p/oFUlvytmWGCY2WzQ');
INSERT INTO loan_transactions VALUES ('16','3','1','ShtvLN6Kmz9c2n5rDYkS79tbETwiTcmuq28s0u/iYnw11MYb3rimXKT//Uq8zseOmfEXbQGwewZhrRTdGYYUj+KEdTsFszw/IOi7HmtY+YCIV+tGhP0rvtYT0BKKDCeK','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS722+jfJUIL8WmIKoyTKv+pWkazQqBFtnCKatvSpRP/evS6Q1m4BTJj4CdJHkIMsfUN28g9Y3Dqennsga6pjURiV9CqsmxXTRzQUgRI/0pIrl','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS7+0QNHFiXuG2l/f/ffCSalYZ9Z1kFnVAoa4cS7zHLgGsCpAygy2sU/E2J1MAuAqDVNGbju0mA/kv3H0oSIXtj7UDfCFzPBaf/bOfoJG0W1hG');
INSERT INTO loan_transactions VALUES ('17','3','1','ShtvLN6Kmz9c2n5rDYkS73qANRjKcYGsZ27zRw6xH0UvKkKvNKxbbsblU+UqhEvRCaiQIntgQZSgBjCTPjbyUeejA0q0nGgjif4K8wn7pfXS8GvwXvs0V2XU6bLposn5','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS72WzBzXkPJDrCENcTT7lcqftdl52xsyXddlXMHObQKc+aIGB1t+ggHtW8j3gwhLpr2zjEEh5VRgIZATnbYxZlrm1InHm8PBdCTGL3TWaVdFL','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS7/2uQnIQYf44C1m+R80bIZzzqnD4Yi6x77uGSI7WJCaTgdykhp4p+ZS5v+PHvdzJ46cpvC0TmN/928T+QJ62wCXRYzU3rg02IdgrA4yZ4KJg');
INSERT INTO loan_transactions VALUES ('18','3','1','ShtvLN6Kmz9c2n5rDYkS74tGglKj7dOYNX9fWkzDRqH9+R7IOsLlLsFpdR+mx3L1fjeLPkVLHFwP9sedhcXkAcWQI2u0yiMvu8SBCiFKUL2b9w5GHmOQDpNHK+F8JzxxctI9+MoVhU4NZjuTz4zebA==','ShtvLN6Kmz9c2n5rDYkS79MTsWXlSoUyKqW4NqCIxWPtZ73VEJGE2k7HUC1Fwlv3xYoADh4oMnupHXTOKbHEG9ipv7NblnGKVaa/SCxq9Z6fXmJmRyF5n9LxVVLFvFaq','ShtvLN6Kmz9c2n5rDYkS7zBu8AxBr4pVTDwdlLNQGxw3J2oNjBfoKuqm8RRtDVDvmm4/Izk9PrFiDLPhPOi71m71FXuqM9pG68ZOWZnBIkp05jyu7G1n0NiUmVdjOUcl','ShtvLN6Kmz9c2n5rDYkS7ybHNLQlta0x29XhlcXi5OQsNO04LutHNNML94nse8C5fnbyVVI6XmtZcGlogbEjslF25SbVcPu003ZyQVPxtHNCNBhi1+YWx9VoT7kW3sYa','','ShtvLN6Kmz9c2n5rDYkS79MTsWXlSoUyKqW4NqCIxWPtZ73VEJGE2k7HUC1Fwlv3xYoADh4oMnupHXTOKbHEG9ipv7NblnGKVaa/SCxq9Z6fXmJmRyF5n9LxVVLFvFaq','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS76IiMLL2gbsTcPN8Eh73nsutyht4fNmSwyN5/KWrO1oPFw9nhaAfTSL6qYc/7/Ws4spYrlULI++XXfqI0v2IqO5tW9F6qTORQbk0GV11Zq3g');
INSERT INTO loan_transactions VALUES ('19','3','1','ShtvLN6Kmz9c2n5rDYkS766wJ/d4NlLJzznasqNLrYpF3kRV0kROYxsSDWcv2aRPpGJIE3y2vK3QFaKDnWUl38e6LOWV2QJobKFRUDkVpNlcfhIeKxGkBMUjTdphtIP16aiAjbWcdYogVS3dtCdpfg==','ShtvLN6Kmz9c2n5rDYkS74QdpydZwAO3v9ZTJKuMP2nvhptjD9QAlzEQgDHcwyDkbmXvJKdeH4Tb3aiF66g3BFZr5J8MH+v95DkdJO//qk0Tgbl3x7HOC0LiTUi8PJK+','ShtvLN6Kmz9c2n5rDYkS7zBu8AxBr4pVTDwdlLNQGxw3J2oNjBfoKuqm8RRtDVDvmm4/Izk9PrFiDLPhPOi71m71FXuqM9pG68ZOWZnBIkp05jyu7G1n0NiUmVdjOUcl','ShtvLN6Kmz9c2n5rDYkS79CvRDVVkZeHhW3f/iNC9kC2tJgpz52u+pA4QU5xDoJSLoIyKA+bMmgDn+4u265tWt9DhtnmbBRo8t18ppwLC39C0p4RoWrID6DLatqs1h0B','','ShtvLN6Kmz9c2n5rDYkS74QdpydZwAO3v9ZTJKuMP2nvhptjD9QAlzEQgDHcwyDkbmXvJKdeH4Tb3aiF66g3BFZr5J8MH+v95DkdJO//qk0Tgbl3x7HOC0LiTUi8PJK+','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS7xWWiQPGVd2tcqU6qOtzKWAF0TglMIgz8FnmBIdpOFafzXYYSeb7JvwcNZ6JIM0bHTHWv+yG8Pufl4rWan/ZXMWCMYHUwNlYDztZ0VMBzQ+B');
INSERT INTO loan_transactions VALUES ('20','3','1','ShtvLN6Kmz9c2n5rDYkS70JEPB5Ct4Av9XBlvGBnTOqjqKmUFrBSu3CKXtfv57Z71aclAdR14q6wGmZzn40TFCo0SIF+4PzTqlHsErmIhIJjDVkKL4uZmZurSLTXcaUK','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS71oKWdXAHyVqYkF13yjC7GQA6Dw0zoknghbTPOa5Oa/HLtKIooiTe76qNNqO6IX2PXhcTMbFGyDNjk0EylffX0tx2491dIXSz65pDh0weiFd','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS71VoWdq/JYlF/Xj6l+3gRSrJTAaftXjHeceIhenBLD47roLHNcBKfRu7jrkmAsnsZge9+0D3+9DrtWJz8vGOuPY1khLJhfL+kgESeJVJSzof');
INSERT INTO loan_transactions VALUES ('21','3','1','ShtvLN6Kmz9c2n5rDYkS7y0+Yq+SJTlV8ijt21lKqSsaJ6SI8fOydvtQBUkKXlhoUGB/aEzaKS4unr0dfFwnSR8g46EsneLUVVzRj7gSxKFUdcHr4aBIuLImGoqo7Yuh','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS7+aQGNPGaC2MxPuLGFN2kKBLQ7qhISqZ56zB2ZSTBefB6b/b1TTowMzah4o2J6W9K0aeyfw8TTHkg6tsR5/8Kl1gfOy+HvSafZL1l3GDaNn9','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS76BPwmTTjKn0/wxw1YgYs0pnykP38Hg4VwWH7j+5gfUfwVpsO5FU+QSbFD3dW5rswdk9711GpVV1s5OM1HHgoXNSfRXgQA/X/hKn4P9+glyU');
INSERT INTO loan_transactions VALUES ('22','3','1','ShtvLN6Kmz9c2n5rDYkS7y0+Yq+SJTlV8ijt21lKqSsaJ6SI8fOydvtQBUkKXlhoUGB/aEzaKS4unr0dfFwnSR8g46EsneLUVVzRj7gSxKFUdcHr4aBIuLImGoqo7Yuh','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS739VJdlE1d/0RDBM7380RNaJy5aR5mqAJeKXZiOHxGseFlHGtzpWeZJixA0F+t6d22dod+GV4GoAPyvznGQJVTq0qJ4qvZaQ7//Gefwf/RDF','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS71ZHl8/KE/5Q7yxQdgRDGNChOSeiW0iV7qN2m8LFUqYyUCD6PzHPhHqSI1UUyKpSg9GS70u9jC30pCJFKBeFMUo94L9FL/snhYx8bbb6jGHV');
INSERT INTO loan_transactions VALUES ('23','3','1','ShtvLN6Kmz9c2n5rDYkS7y0+Yq+SJTlV8ijt21lKqSsaJ6SI8fOydvtQBUkKXlhoUGB/aEzaKS4unr0dfFwnSR8g46EsneLUVVzRj7gSxKFUdcHr4aBIuLImGoqo7Yuh','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS75LTAbwv4Gb5MOWzqlVg1qE8yYxxM4qdRL3bRvpOabgHnIrwj5+g7MZotabeENMsz0VpkXeRoFvw3BsInMvMIk2md7TJvhkLO4ZC1Y+OC1x/','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS70SJyKtpmYHULpkhVPu4E3QTLsoHwgljxUbYIpWFMyJTUpDFU9pQN3oPzEqwMQusZt7XTy9PDtBOslNHUdCdT/pGNakbtHuf58O5y3AQGG3M');
INSERT INTO loan_transactions VALUES ('24','3','1','ShtvLN6Kmz9c2n5rDYkS7+egZkW9p85EmtJJ4CwoiALLNP4ejVKVbkzflBRizv8g71OVshbsBzjLVohqv97lhQBJC4zCF8/vh60+sqyWxoEvJ8F0xPl+dRUAf4cCMlPT','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS72PlpHNeC/RL1luaytoh/LorpAVY+w8eMpRHHCGb/8QD3AgjkdtoAlv7HwS+nGJJXLvjTzXbve2J0HQ03UNepzEzsffLnqHUitQq2D49cYi6','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS7xJJmyivix24d67++3qpLDa23WDNZXwvs9T/K5rlxCpu9e3gSuTcUlXeIVbP6meKZA8o6YXrOOvxmFYJ2axNsOYbapblJKiuDvCCHRl/swl9');
INSERT INTO loan_transactions VALUES ('25','3','1','ShtvLN6Kmz9c2n5rDYkS71KeJixF4e3mzdE4z+HKXGEFQYY6eeDknJtMkbVy/GKOHlfWoNbHVMCLbrRIcV+ETqKaaKDBTVLpEh1p8xhSnfMTLFkz+esRgCggCgJL3xHz','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS7/Hr8P5CjwAWANvBYE/Dds508oGiHkHvjx7ZqMK/X9tGqhbs0iKsJgNLIJIDY8wRQ9U7QafWfQ5wKxSWxDr+wu+mJI3Ns6nCyvWZBYqrl8ss','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS75hkBZrApCKopj4XoTqzXH37RxNFxBjCGJYdZ81RLP87ApdSdf2XsbN/esq7TKYsn4pmoY3pmeIztkTeV7wVFneqoUYLiPqXwpN0CYfzrpMK');
INSERT INTO loan_transactions VALUES ('26','3','1','ShtvLN6Kmz9c2n5rDYkS7/dGFx9FFQzEZxGVwlTHJ37fFWT4vbIKdeLbAIpeM3bsv/guESIc5RxYBdPbEGo6Ir7IxYfcXvAgRvQqKMxpTLZmPSi7NqNG8brNYgAVdyYA','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS71xlXhPYDiWn8BmmOlvAZ/39tB59d9Tqm2k0qH6af2DbXzy+le/s/VDc6VO5hEOOIMVVbAhSw7p7xv4Fwfp2o2G0Ofl9qb2h9KD9cG6P+DpQ','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS7zpLrYSJ7Yjqva7mhDqkxgySaU+wZugQuu49wtLfCCXleoOyQDGU4+dx/ABE8be76iIwSX13yImxyxHOoTBxB02wsE+zHDMQDSE6EvRmiQ/3');
INSERT INTO loan_transactions VALUES ('27','3','1','ShtvLN6Kmz9c2n5rDYkS70JEPB5Ct4Av9XBlvGBnTOqjqKmUFrBSu3CKXtfv57Z71aclAdR14q6wGmZzn40TFCo0SIF+4PzTqlHsErmIhIJjDVkKL4uZmZurSLTXcaUK','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS74bKeba8n8jizgkm+vTlq9cxlVJemU6YJ46LzNH8zuDrS0TUSdIWTgtNo92bBeLetJPXu2og1y5Kcd3ARLuRbnZPmGqem/gRE4b91ic4FTon','','ShtvLN6Kmz9c2n5rDYkS7/9HwdvWZ7dOKdT0XIf5Mvi4c8dPxDCPcUz08BZfRTFMmZpSY6kb5c+p+AXPL/Y7fGFr4s7bJTfys9rJOoV05c9f/VypnYJ3BOXKdxXpV96O','ShtvLN6Kmz9c2n5rDYkS74bKeba8n8jizgkm+vTlq9cxlVJemU6YJ46LzNH8zuDrS0TUSdIWTgtNo92bBeLetJPXu2og1y5Kcd3ARLuRbnZPmGqem/gRE4b91ic4FTon','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS7+IYsMvYzwMdxwS1flEe8O0UscHBTy0eSH5xrtZOdGijgyDkSGYMXHVqV1pPPpgCf6PUQFni9X8SodZoVuUlIFR/vOOc+MnHkvDzMD8LbGW8');
INSERT INTO loan_transactions VALUES ('28','3','1','ShtvLN6Kmz9c2n5rDYkS78YDBt2NaX/BSOIHJTxJ8vKYplPMKAvWDCVNYMYbems4/TMX8LUdEucP4+7XhziQic6H20k8WkzQ1vlbY9qq5cYqX9B4i2V6DGwGwxtkGJYI','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS7+IYsMvYzwMdxwS1flEe8O0UscHBTy0eSH5xrtZOdGijgyDkSGYMXHVqV1pPPpgCf6PUQFni9X8SodZoVuUlIFR/vOOc+MnHkvDzMD8LbGW8','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv');
INSERT INTO loan_transactions VALUES ('29','4','1','ShtvLN6Kmz9c2n5rDYkS70D5soVuRJYEmiwnaQvbzkS9KX/Q79jHwKdMXX1JAl+1ot+wEW1cGPfflrjgIE1WzYrEyF1EZ0ej5QBHfrm4cjr27Y7fWk+tJ+i3YOAJCW9VowAzNtQ46vUch7I3uuxkVQ==','ShtvLN6Kmz9c2n5rDYkS715mOMyO+MP5n+QGGDuqypvO1XBuGIcA0NKmBO3wtaQNxH8lM/Pd85xME6yuVGb5TnVWRlTPhpfVSOgarH9YaYdyirEwLQHG20QA0Nx/3p1E','ShtvLN6Kmz9c2n5rDYkS70C6/kFbWQ+elAnPuH3BrF9BHRzT+m4H8V9cIcW5v8rW2rKZozpc/wPycc5qN71fKK1iSa9FP22xBoXFKMdf3NgiBzT1CNEV0iJf2aJ1WS/0','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','','','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH');
INSERT INTO loan_transactions VALUES ('30','4','1','ShtvLN6Kmz9c2n5rDYkS70JEPB5Ct4Av9XBlvGBnTOqjqKmUFrBSu3CKXtfv57Z71aclAdR14q6wGmZzn40TFCo0SIF+4PzTqlHsErmIhIJjDVkKL4uZmZurSLTXcaUK','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS73o3OTNwS8LxDacgvhw8wiMezxO91TE3JPARySKWW1yiLm7xHpwOvniwcJbY7CaVmVtWSn+1uCfSwu2OT55ot/4glQQqhAXeHuNgdR15sq7Q','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS74Neo0cdkNI/1BMkCYB+Gj9HYiexYlLFRMwCPiTWGMtb+clUYhYHHcRuxoHLqc/99v6M2Y92EdnxkL1CLMpoCi0QKZAM2SbINwBvG+iz8fqx');
INSERT INTO loan_transactions VALUES ('31','0837','','ShtvLN6Kmz9c2n5rDYkS78YDBt2NaX/BSOIHJTxJ8vKYplPMKAvWDCVNYMYbems4/TMX8LUdEucP4+7XhziQic6H20k8WkzQ1vlbY9qq5cYqX9B4i2V6DGwGwxtkGJYI','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS78J6bH6BUaGWkl1/1qyvyQGUQO3Uw1ekf1wQTjSUXqjBkOmEmKHOOT29xDmJO7qheM/ekC2Ppm1WgXcAUbwTGSNAIltAoE2C+q0NeqWLHEnN','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS762YZJuiDypBkxA/GFsYYl1n6FG7XWOiRV+Sn1/qykZzqF0X2Iy+wLyOdhPF6itH/UT81ptn4RRaqiT6BRvK5Gj2+GZ36TranLgcFhGRUsjX');
INSERT INTO loan_transactions VALUES ('32','4','1','ShtvLN6Kmz9c2n5rDYkS78YDBt2NaX/BSOIHJTxJ8vKYplPMKAvWDCVNYMYbems4/TMX8LUdEucP4+7XhziQic6H20k8WkzQ1vlbY9qq5cYqX9B4i2V6DGwGwxtkGJYI','ShtvLN6Kmz9c2n5rDYkS74hQW9gN1bGblMKT3cII+phClt5OXjUaZRfo2WJsC5IhaN71yAVwZEruQATxc2QIhrA8CshQ5/wLz++n/BQ9ldyiLrhwHPI6wRSpk3R6mE+m','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','','ShtvLN6Kmz9c2n5rDYkS78IEocAhQR0ji8qoit+6EQvyDSVl+KSVrUTsdeplYia9Rj+qruuiIDpVEpFqWgEm/7TE3x65dfVC4d13atALwXK7F6JbI6G7Nzl2PC/OiJJQ','ShtvLN6Kmz9c2n5rDYkS72Jbtx8LUjUHG4SCgWM8iGyk63N25E45JuHn2iZ9yZxOd9VbDN17oMUV+WQvUmiTQDdJC+QjzEAEHbf+RPAjYcGwmlhU8WK1N3wWWz3dmgY4','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn');
INSERT INTO loan_transactions VALUES ('33','4','1','ShtvLN6Kmz9c2n5rDYkS7zR+9hUTSIsrzZPM1LXCETJmsh34VbRG3hGXQzZAReg1DaOzvsa35TU1vWmEjv+SROF3PgYJSo2e2r7lfFNQgYSeC2NXOLYx9BBmWtUIoXwNwFROHWlNdYNePtR5kDCvIg==','ShtvLN6Kmz9c2n5rDYkS74QdpydZwAO3v9ZTJKuMP2nvhptjD9QAlzEQgDHcwyDkbmXvJKdeH4Tb3aiF66g3BFZr5J8MH+v95DkdJO//qk0Tgbl3x7HOC0LiTUi8PJK+','ShtvLN6Kmz9c2n5rDYkS70C6/kFbWQ+elAnPuH3BrF9BHRzT+m4H8V9cIcW5v8rW2rKZozpc/wPycc5qN71fKK1iSa9FP22xBoXFKMdf3NgiBzT1CNEV0iJf2aJ1WS/0','ShtvLN6Kmz9c2n5rDYkS78J6bH6BUaGWkl1/1qyvyQGUQO3Uw1ekf1wQTjSUXqjBkOmEmKHOOT29xDmJO7qheM/ekC2Ppm1WgXcAUbwTGSNAIltAoE2C+q0NeqWLHEnN','','ShtvLN6Kmz9c2n5rDYkS74QdpydZwAO3v9ZTJKuMP2nvhptjD9QAlzEQgDHcwyDkbmXvJKdeH4Tb3aiF66g3BFZr5J8MH+v95DkdJO//qk0Tgbl3x7HOC0LiTUi8PJK+','support@essentialapp.site','ShtvLN6Kmz9c2n5rDYkS70br0l9+Qdctd2P21FgboYkYZOnu0K4oUSVu1Va3wj8sv7ntK8lgP6+LozW6yWtKqEsuEb+avBCHyDYQQmWmkixUhjmNbW9Oqf2NRqF9l9XO');
DROP TABLE IF EXISTS loans;
CREATE TABLE `loans` (
  `s_no` text DEFAULT NULL,
  `loan_no` int(11) DEFAULT NULL,
  `customer_no` mediumtext DEFAULT NULL,
  `customer_name` mediumtext DEFAULT NULL,
  `customer_phone` mediumtext DEFAULT NULL,
  `loan_product` mediumtext DEFAULT NULL,
  `loan_type` text DEFAULT NULL,
  `loan_amount` mediumtext DEFAULT NULL,
  `loan_term` mediumtext DEFAULT NULL,
  `no_of_installments` text DEFAULT NULL,
  `loan_interest` mediumtext DEFAULT NULL,
  `loan_installment` mediumtext DEFAULT NULL,
  `principalBal` text DEFAULT NULL,
  `interestBal` text DEFAULT NULL,
  `gross_loan` mediumtext DEFAULT NULL,
  `loan_balance` mediumtext DEFAULT NULL,
  `loan_applicationDate` mediumtext DEFAULT NULL,
  `take_home` mediumtext DEFAULT NULL,
  `loan_reviewer` mediumtext DEFAULT NULL,
  `loan_reviewDate` mediumtext DEFAULT NULL,
  `loan_approver` mediumtext DEFAULT NULL,
  `loan_approvalDate` mediumtext DEFAULT NULL,
  `loan_payments` mediumtext DEFAULT NULL,
  `firstRepaymentDate` text DEFAULT NULL,
  `repaymentFrequency` text DEFAULT NULL,
  `last_paymentDate` mediumtext DEFAULT NULL,
  `loan_status` mediumtext DEFAULT NULL,
  `loan_form` text DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  `loan_classification` mediumtext DEFAULT NULL,
  `days_inArrears` mediumtext DEFAULT NULL,
  `amount_inArrears` mediumtext DEFAULT NULL,
  `worst_classification` mediumtext DEFAULT NULL,
  `worst_daysInArrears` mediumtext DEFAULT NULL,
  `loan_writeoff` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO loans VALUES ('1','1','1','ShtvLN6Kmz9c2n5rDYkS74OtBJLWrMJ0g/d09kNlMHa4BQVl5Zh0nDVCWjEXBYkIzN7K05ROctAdvAkfE6n9xPJ2sTsSUNnE9FNskVM6c5QbJxnNmg939PW2dD8/wF2I','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS77oPmOGxuU4c6wSOPXw9ZxQ77eSoGQfh0W2McsG4lzrEjHM4DX5RxjNXBu/gZdj9aWHlJApove9qrQ0gkGeW0Rf0LaubMQEIHdLf6VRLU4PP','ShtvLN6Kmz9c2n5rDYkS715mOMyO+MP5n+QGGDuqypvO1XBuGIcA0NKmBO3wtaQNxH8lM/Pd85xME6yuVGb5TnVWRlTPhpfVSOgarH9YaYdyirEwLQHG20QA0Nx/3p1E','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS74F/t0sPLXYaPWaPmiaEu2bH2PaA1yS7gJPixbhtG4mGVaiJlihEGuo33EI+nVhfBala4oHwvv/t/quuNPs6egYEbM28O7JDyKzrMqcSdcNV','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS78As+glD9pxV4PCKmgvrTGuP4cWX+NR6euMaPQQWmIkqdZrsGxMaIu2s7FqPyhfENJQD8g4xnEmQZeU/RIT0rQzmVxvUc8B5RWVkM1FpkY39uqTkT68ntO8p6CXjAq4EmAU9WLmjZZaQlj5R1k3jTiAf+KDRCRFpcpcV1VpIETvdRmTHosFRkewp7RC5An7xPWyrpi5t6SvkEQ70AqJBYa67iWRWH6BOZs22U1AqfwzwOHU+RhxA2v2ebGxSSfnWBz2dJUZpwnON5l8hcqBu8LQ=','','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv','ShtvLN6Kmz9c2n5rDYkS7wOrL49C2UQXp2gUgLOsGDN5ApS4YxhaCbK6kKn53N8s0moVmaid9o1YhKDCFZ6U3FfKlVDp6dfghAYVjsi0IRKzaejJyKFpM27PSgx5SgV7mCny/iDs5JjF6fEJGj28Gg==','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7wF6tLe7rRgz2ttag2XKZINgGfGG5VUYSeg3pbNFVq3a4A6KYWaZDTiJ09bMBdg6Jg4i2sQSrhk1Uj5GuAAwLJ/NLWrwRoLS2kH3ocop/1ml0LOA2zVIL/GsT50nPtTItA==','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS79yszrUsAqeQV/RkjFVSpcBEeN6Ai/7dJuREgsfbEOr2JrHI+n2o9coH4YPMdPSEU5T94mT498mPsEyasVcG6AaDCjKHsV9MY+isokvWp6uifRwtolIcJd4LRbJqrnBAuA==','ShtvLN6Kmz9c2n5rDYkS72dsPl6rTmvGOKs3Cguf6m5Maxph+CWq3mkE0WxukohDAQWhgzOvp+Hl2giZYCYvAvMVaxeUjRQtKVHIa+3FWOqKPC/UssjC1Q32bfdbmUpD','ShtvLN6Kmz9c2n5rDYkS71Z8dYIK75mwdp4LZKyX5ISPZxnoC8TLMBa7eQtXO8gqTtZCDJaI8+hJgtjmgEvgi3JBz/85OW3kXL85ImCxToSRLYU5UVGy+NMMJNQtWXNq','ShtvLN6Kmz9c2n5rDYkS77Vxs+7JnSqh7gdTdvExya8RVOO0JQ3Ye0k1kLcgo8ripq82Mur9HM5AWPKnlw6qznRbIMXFO6nD231fWFpCcwQB/F+vceKTAkTZbZEGkCFR','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS72X5YERDhdxFWDlzR4V+/mwYnjZ2Xzbx4Dbkny7shIo7IviwNAzCgaF1IabMx4zIbrgEWhf2s9r60dojcalqdee8FuMV7f85rY0LCw++ZcB1','ShtvLN6Kmz9c2n5rDYkS7xRvoTiCJ5MTm5G6w2wSraE3TT0vxVepRlU8TqFaGUByiDA/rRv1VU5ngZre8tNiTFsdisNTVTvQfRBn5a1jyNy3rgq3KvtEMQ4O+TclO2P45BIX6IkCKaSj02o6cK+hQ78Bfr0lguIv8pGCtoetPTN4VMHiJF8+4Rvlj/bXtBlq','Machakos','','','','','','');
INSERT INTO loans VALUES ('2','2','1','ShtvLN6Kmz9c2n5rDYkS74OtBJLWrMJ0g/d09kNlMHa4BQVl5Zh0nDVCWjEXBYkIzN7K05ROctAdvAkfE6n9xPJ2sTsSUNnE9FNskVM6c5QbJxnNmg939PW2dD8/wF2I','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS77oPmOGxuU4c6wSOPXw9ZxQ77eSoGQfh0W2McsG4lzrEjHM4DX5RxjNXBu/gZdj9aWHlJApove9qrQ0gkGeW0Rf0LaubMQEIHdLf6VRLU4PP','ShtvLN6Kmz9c2n5rDYkS715mOMyO+MP5n+QGGDuqypvO1XBuGIcA0NKmBO3wtaQNxH8lM/Pd85xME6yuVGb5TnVWRlTPhpfVSOgarH9YaYdyirEwLQHG20QA0Nx/3p1E','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS74F/t0sPLXYaPWaPmiaEu2bH2PaA1yS7gJPixbhtG4mGVaiJlihEGuo33EI+nVhfBala4oHwvv/t/quuNPs6egYEbM28O7JDyKzrMqcSdcNV','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS78As+glD9pxV4PCKmgvrTGuP4cWX+NR6euMaPQQWmIkqdZrsGxMaIu2s7FqPyhfENJQD8g4xnEmQZeU/RIT0rQzmVxvUc8B5RWVkM1FpkY39uqTkT68ntO8p6CXjAq4EmAU9WLmjZZaQlj5R1k3jTiAf+KDRCRFpcpcV1VpIETvdRmTHosFRkewp7RC5An7xPWyrpi5t6SvkEQ70AqJBYa67iWRWH6BOZs22U1AqfwzwOHU+RhxA2v2ebGxSSfnWBz2dJUZpwnON5l8hcqBu8LQ=','','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv','ShtvLN6Kmz9c2n5rDYkS78n6i4y9r8EHoo2uSyHmsRU5yrTabSzE0aenD/ygO9Hhgj0lxcsqIlf/1mh88DkGeIqQ8qA/Kt4DXnH3TUtl+HaPc+qytQHtGNjgy68ONTe5NG34XAaVDfP/sbZzLk8ivg==','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS70V0nc0BENVeVKgVpq90tR1k3Wug+FIlitfW40vql3Bth0CgmqLbD7QynaqXq/eqSQ3iBt/fbNY4aqMaA5BLdgyPc+qytQHtGNjgy68ONTe5g6YyxOTp6U++vcmp36nJ5g==','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS77JDyjRtubwU7wLaSIr/r1Af/RUZyCNkE4M/Mju6p2/5LFweIA399eH8jRlAtXzArjvBGmhjAz/s9Xon9JfQXJiPc+qytQHtGNjgy68ONTe5J8lFm/vJbQQlzyczSo2+mw==','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS77Vxs+7JnSqh7gdTdvExya8RVOO0JQ3Ye0k1kLcgo8ripq82Mur9HM5AWPKnlw6qznRbIMXFO6nD231fWFpCcwQB/F+vceKTAkTZbZEGkCFR','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS72X5YERDhdxFWDlzR4V+/mwYnjZ2Xzbx4Dbkny7shIo7IviwNAzCgaF1IabMx4zIbrgEWhf2s9r60dojcalqdee8FuMV7f85rY0LCw++ZcB1','ShtvLN6Kmz9c2n5rDYkS72S0yIwBeUSEAL6jdRBHb83KDPHqkkG24fKcOQBd4xALsfovWXSgqGnxcbWBbBZ8dNAAGpFf4CZ7hMx3bG8MQJO3rgq3KvtEMQ4O+TclO2P45BIX6IkCKaSj02o6cK+hQ18QGm6YmQLoyrDmIzPXOMmPtJoLJtHNmLSJGrwKLBJM','Machakos','','','','','','');
INSERT INTO loans VALUES ('3','3','1','ShtvLN6Kmz9c2n5rDYkS74OtBJLWrMJ0g/d09kNlMHa4BQVl5Zh0nDVCWjEXBYkIzN7K05ROctAdvAkfE6n9xPJ2sTsSUNnE9FNskVM6c5QbJxnNmg939PW2dD8/wF2I','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS77oPmOGxuU4c6wSOPXw9ZxQ77eSoGQfh0W2McsG4lzrEjHM4DX5RxjNXBu/gZdj9aWHlJApove9qrQ0gkGeW0Rf0LaubMQEIHdLf6VRLU4PP','ShtvLN6Kmz9c2n5rDYkS7wQPSwJBxdAP9JoFEmroh60jSIIa3E3ucB300xW6LEoxumqiCSRFb8Q0xgbCyOEtJ2tHEez0qJIopubkqPypLKkiKO+reXmG+G5fkukBLShG','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS74F/t0sPLXYaPWaPmiaEu2bH2PaA1yS7gJPixbhtG4mGVaiJlihEGuo33EI+nVhfBala4oHwvv/t/quuNPs6egYEbM28O7JDyKzrMqcSdcNV','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS78As+glD9pxV4PCKmgvrTGuP4cWX+NR6euMaPQQWmIkqdZrsGxMaIu2s7FqPyhfENJQD8g4xnEmQZeU/RIT0rQzmVxvUc8B5RWVkM1FpkY39uqTkT68ntO8p6CXjAq4EmAU9WLmjZZaQlj5R1k3jTiAf+KDRCRFpcpcV1VpIETvdRmTHosFRkewp7RC5An7xPWyrpi5t6SvkEQ70AqJBYa67iWRWH6BOZs22U1AqfwzwOHU+RhxA2v2ebGxSSfnWBz2dJUZpwnON5l8hcqBu8LQ=','','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv','ShtvLN6Kmz9c2n5rDYkS70rWqntzKYOzKBEbCVOLYJ+sZf89qYZzM1u9qJQWvgUiNyX61QBJrpWOMlFqwl98KKwJSgOigk9db/098gOZIuEnK7dYjk0JJqpxnR7IzjLyJE8TWwX/jVQ2xxH0XSct7w==','ShtvLN6Kmz9c2n5rDYkS77xgfjrXomEtFpXYnBM3vdZUoI06WvLpVkOjvTFTm0XKns1MbALJFHPQ0UoeJXPXhQ46SH9fFyvy5E3YLYYRA4ePq1CoET94zCSRqqHZjgqw','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS72rH1RDQgj08T8RMMySWPiH0dXRdP4Hu9SfyYhsNe53+pI6kdm/cnRiGASG3buZiWYVoTRluf5UEs6Pku5n3yOtNPfICNZq+uBEWMjHI3w6I6erqTr0nvnxlaH3eS7THdg==','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7ytgaubSjWGgveq8JemUQivvtcJeBJ+VCYET/rtQLmIvHV43DtLPo4Cpd9jGxxwK0GILqqmqIiN43+rMkVPw7eJNPfICNZq+uBEWMjHI3w6IH/XkuO2wLRLqx4p86gLj0w==','ShtvLN6Kmz9c2n5rDYkS7+FrhUOKrTe5hL09czF0o0OruJ/bFB+nHJpbnnjx4t+X1O8uyqL63QFSiVkXBg2Yc2enetHgBF3yHsdHSWv7K38FUFOnx4SbiU2IAkEJJq0y','ShtvLN6Kmz9c2n5rDYkS73b2lE6GUU9PlNMODhIZlAzi/80p7u0IADkprrTFo5C/AraiUqnq1fVOTsrYcbpZOrIw9LBu+7J5EagOdEPAEbjEg9e1j5Up7bArN04K6zvg','ShtvLN6Kmz9c2n5rDYkS77Vxs+7JnSqh7gdTdvExya8RVOO0JQ3Ye0k1kLcgo8ripq82Mur9HM5AWPKnlw6qznRbIMXFO6nD231fWFpCcwQB/F+vceKTAkTZbZEGkCFR','ShtvLN6Kmz9c2n5rDYkS78YDBt2NaX/BSOIHJTxJ8vKYplPMKAvWDCVNYMYbems4/TMX8LUdEucP4+7XhziQic6H20k8WkzQ1vlbY9qq5cYqX9B4i2V6DGwGwxtkGJYI','ShtvLN6Kmz9c2n5rDYkS72X5YERDhdxFWDlzR4V+/mwYnjZ2Xzbx4Dbkny7shIo7IviwNAzCgaF1IabMx4zIbrgEWhf2s9r60dojcalqdee8FuMV7f85rY0LCw++ZcB1','ShtvLN6Kmz9c2n5rDYkS786mVvfaJFpDCUOKPvChSJNN1Lph12ISKrWn2JxV5l/F4oJwKMUC5i8mSAIDm4LPQdMpVQF7KtH0lqiQBKJIwGu3rgq3KvtEMQ4O+TclO2P45BIX6IkCKaSj02o6cK+hQ4fhLj2QI+axNfbWDXJ5NhbJvz02SYhGypfRqAcKNoE2','Machakos','','','','','','');
INSERT INTO loans VALUES ('4','4','1','ShtvLN6Kmz9c2n5rDYkS74OtBJLWrMJ0g/d09kNlMHa4BQVl5Zh0nDVCWjEXBYkIzN7K05ROctAdvAkfE6n9xPJ2sTsSUNnE9FNskVM6c5QbJxnNmg939PW2dD8/wF2I','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','ShtvLN6Kmz9c2n5rDYkS77oPmOGxuU4c6wSOPXw9ZxQ77eSoGQfh0W2McsG4lzrEjHM4DX5RxjNXBu/gZdj9aWHlJApove9qrQ0gkGeW0Rf0LaubMQEIHdLf6VRLU4PP','ShtvLN6Kmz9c2n5rDYkS715mOMyO+MP5n+QGGDuqypvO1XBuGIcA0NKmBO3wtaQNxH8lM/Pd85xME6yuVGb5TnVWRlTPhpfVSOgarH9YaYdyirEwLQHG20QA0Nx/3p1E','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS74F/t0sPLXYaPWaPmiaEu2bH2PaA1yS7gJPixbhtG4mGVaiJlihEGuo33EI+nVhfBala4oHwvv/t/quuNPs6egYEbM28O7JDyKzrMqcSdcNV','ShtvLN6Kmz9c2n5rDYkS76aJmsdPpK3iSUWr9xQOOTZklFmNYaqU1u/uTT8Bf3qE9QijB9eUqYn59rmgkL/tvrTOLqnqkLkDth2GE/otoXLXeiYOEGWC8Fgt/lOM2IGc','ShtvLN6Kmz9c2n5rDYkS78As+glD9pxV4PCKmgvrTGuP4cWX+NR6euMaPQQWmIkqdZrsGxMaIu2s7FqPyhfENJQD8g4xnEmQZeU/RIT0rQzmVxvUc8B5RWVkM1FpkY39uqTkT68ntO8p6CXjAq4EmAU9WLmjZZaQlj5R1k3jTiAf+KDRCRFpcpcV1VpIETvdRmTHosFRkewp7RC5An7xPWyrpi5t6SvkEQ70AqJBYa67iWRWH6BOZs22U1AqfwzwOHU+RhxA2v2ebGxSSfnWBz2dJUZpwnON5l8hcqBu8LQ=','','ShtvLN6Kmz9c2n5rDYkS713zTXon7KqMIhgITFUYpkXW4pO5l/o/xQoF8DVVcuK1Dt0pfE8jSDsbtfCT9u9mG/JO2Sy1zzchSPpgc9oekwKar1FFGi966NKB82OdbBsH','ShtvLN6Kmz9c2n5rDYkS70br0l9+Qdctd2P21FgboYkYZOnu0K4oUSVu1Va3wj8sv7ntK8lgP6+LozW6yWtKqEsuEb+avBCHyDYQQmWmkixUhjmNbW9Oqf2NRqF9l9XO','ShtvLN6Kmz9c2n5rDYkS71Vj5lA9Jq0eb2o78wcSL+3DwSiK6ha5kiCHGAGw8r3t8zfTdXgZsJPRlnpEEHmXTkIjGQR+VSFmerTQqqUEa7r27Y7fWk+tJ+i3YOAJCW9Vg3vv8hA+79GZg/2FejO9Hw==','ShtvLN6Kmz9c2n5rDYkS765VrAS1jZkty1i/lahfDJ6V8G+h0cA3bA2e62XXGa4mwqGrzcdW9kGsdDVFg2nf/Snb6nAWGGv/nVIEgkZmz5sMtTpzksjo0WMrynJdEyYn','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7zBTKyFvyWE0llOwdmSVCoT2pmssH8+MNlMbqMp6oCCJAybV091fdrAO7CiCtC0VRbp5UxfWZEhloFS4oTKU52f27Y7fWk+tJ+i3YOAJCW9Vdss+Ixp9yIS4RX3SpaCobA==','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS70D5soVuRJYEmiwnaQvbzkS9KX/Q79jHwKdMXX1JAl+1ot+wEW1cGPfflrjgIE1WzYrEyF1EZ0ej5QBHfrm4cjr27Y7fWk+tJ+i3YOAJCW9VowAzNtQ46vUch7I3uuxkVQ==','ShtvLN6Kmz9c2n5rDYkS73UdnVktc8fzYWAPbOHuA3CbkXnoBEcp3rUN9w4FLhQ6COUeHAgr5uezDkXznUZeNQ4H4QUFfQlzxsPz7IAGZ5jRFYw4navOFGY8ItjIB114','ShtvLN6Kmz9c2n5rDYkS78Ispkh8pVWjlVgxJVYJNM1s1Qlxp5fwlR8jFEKhdxyRsBqIOeYxGi0FcbA6k75Dq5rwlWFTTf48OtbJBIUiOkv9Z2eESDV66OtmUDCBBzpn','ShtvLN6Kmz9c2n5rDYkS77Vxs+7JnSqh7gdTdvExya8RVOO0JQ3Ye0k1kLcgo8ripq82Mur9HM5AWPKnlw6qznRbIMXFO6nD231fWFpCcwQB/F+vceKTAkTZbZEGkCFR','ShtvLN6Kmz9c2n5rDYkS78YDBt2NaX/BSOIHJTxJ8vKYplPMKAvWDCVNYMYbems4/TMX8LUdEucP4+7XhziQic6H20k8WkzQ1vlbY9qq5cYqX9B4i2V6DGwGwxtkGJYI','ShtvLN6Kmz9c2n5rDYkS78m9ScrTA1pBXtG+Ix2Z7xtW/DWnxG/7uvJH4suIZxZQihJwNwZkTXPCQUGxljp1UDX3GoTCIBvOmtGNaJIWR/sfkRxobkcS86k1LON+0VvE','ShtvLN6Kmz9c2n5rDYkS73+WYn2NlHHP5mCAN4tVJM1HfF5ExaBwaBi3ax96KUGoU4Mld/IamEkj1M9Xxlm6krmCkZCZ7EW5CT/cAkd+hO23rgq3KvtEMQ4O+TclO2P45BIX6IkCKaSj02o6cK+hQ1tgCoCOC4YhYHFJJqC+fS9gm6USnNMY6cpibCWDJK/D','Machakos','ShtvLN6Kmz9c2n5rDYkS76YCz03nJJ7aq7wEvcXzt0fGOg3wdmL4meWeyGAyXF2jKFzceACcyH4zuIhIdkxyu2844m2S28sU4VA7CW4tjV/taO956+eUPQN2KJCJF6aF','ShtvLN6Kmz9c2n5rDYkS7xsoZfYrZth1yhHCrR+scg2d8gXvSIpmx2dNAaSaWbHs3+CoExQRRtCieBWZovBnfiUspj2hBWkCWimFJD884+tdGzHBJk+/PFCfhvYxxoiv','ShtvLN6Kmz9c2n5rDYkS72dWFC5uwvy+7GFMTEzEcLG7sMJqmHAhulod56KD+0TICTJsPJ3Iqxw0lz8rnlDrqnuzwprbfltBSMEJdf6OWjvW+WeoLGExra1It8nzp4nj','ShtvLN6Kmz9c2n5rDYkS7zqbD2rDWnu0d0FQ++/EfPl9etFJAKcNdfNop57ch90rH1gx0n13ZzhOVu7jdh8bramxSi4hCEzpzyyt7YSSahwcbjbF7rq3+tb9ZB05Neg3','ShtvLN6Kmz9c2n5rDYkS7/lsvjhkzkE9qDmpSC20Q+BWZ6wK7l2yKqQQnVDMsPcWz0rMHpF7FExFPZKcBFNF3PQ2s8G231mAjnwLeZ7eQJZy/UinPpZPX8y9jQ8O50Bo','');
DROP TABLE IF EXISTS location;
CREATE TABLE `location` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `location_name` mediumtext NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO location VALUES ('1','Machakos','Triple M building, Room B10');
INSERT INTO location VALUES ('2','Tala','Equity bank buiding');
DROP TABLE IF EXISTS member;
CREATE TABLE `member` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `staff_no` mediumtext DEFAULT NULL,
  `staff_name` mediumtext DEFAULT NULL,
  `staff_phone` mediumtext DEFAULT NULL,
  `cumm_savings` mediumtext DEFAULT NULL,
  `cumm_withdrawals` mediumtext DEFAULT NULL,
  `savings_bal` mediumtext DEFAULT NULL,
  `cumm_shares` mediumtext DEFAULT NULL,
  `transfered_shares` mediumtext DEFAULT NULL,
  `shares_bal` mediumtext DEFAULT NULL,
  `cumm_dividends` mediumtext DEFAULT NULL,
  `paid_dividends` mediumtext DEFAULT NULL,
  `dividends_bal` mediumtext DEFAULT NULL,
  `cumm_loans` mediumtext DEFAULT NULL,
  `cumm_repayments` mediumtext DEFAULT NULL,
  `loan_bal` mediumtext DEFAULT NULL,
  `date_modified` mediumtext DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS mpesa_collections;
CREATE TABLE `mpesa_collections` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` mediumtext NOT NULL,
  `state` mediumtext NOT NULL,
  `provider` mediumtext NOT NULL,
  `charges` mediumtext NOT NULL,
  `net_amount` mediumtext NOT NULL,
  `value` mediumtext NOT NULL,
  `account` mediumtext NOT NULL,
  `api_ref` mediumtext NOT NULL,
  `clearing_status` mediumtext DEFAULT NULL,
  `mpesa_reference` mediumtext DEFAULT NULL,
  `failed_reason` mediumtext DEFAULT NULL,
  `failed_code` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO mpesa_collections VALUES ('1','08PZXJ0','COMPLETE','M-PESA','0.00','500.00','500.00','254722842629','MFI-SMS','AVAILABLE','SCB5OEUAMZ','','');
INSERT INTO mpesa_collections VALUES ('2','KOWD430','COMPLETE','M-PESA','0.00','40.00','40.00','254725887269','MFI-SMS','AVAILABLE','SCB5OYB6LJ','','');
INSERT INTO mpesa_collections VALUES ('3','Y7PW2L0','COMPLETE','M-PESA','0.00','41.00','41.00','254725887269','MFI-SMS','AVAILABLE','SCB5OZ0XH1','','');
INSERT INTO mpesa_collections VALUES ('4','YE37B7Y','FAILED','M-PESA','0.00','42.00','42.00','254725887269','MFI-SMS','','','Request cancelled by user','1032');
INSERT INTO mpesa_collections VALUES ('5','KOWGP40','FAILED','M-PESA','0.00','42','42.00','254725887269','MFI-SMS','','','Request cancelled by user','1032');
INSERT INTO mpesa_collections VALUES ('6','Y3QLZEY','COMPLETE','M-PESA','0.00','11','11.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCG09B1PH2','','');
INSERT INTO mpesa_collections VALUES ('7','YRDRG90','COMPLETE','M-PESA','0.00','11','11.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCG79BR6XB','','');
INSERT INTO mpesa_collections VALUES ('8','Y7PB370','COMPLETE','M-PESA','0.00','12','12.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCG19CHS19','','');
INSERT INTO mpesa_collections VALUES ('9','Y3QLZRY','COMPLETE','M-PESA','0.00','42','42.00','254725887269','MFI-SMS','AVAILABLE','SCG79CR0GN','','');
INSERT INTO mpesa_collections VALUES ('10','YMN9EX0','COMPLETE','M-PESA','0.00','42','42.00','254725887269','MFI-SMS','AVAILABLE','SCG39FBBA7','','');
INSERT INTO mpesa_collections VALUES ('11','Y6P977K','COMPLETE','M-PESA','0.00','11','11.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCG19MDUJD','','');
INSERT INTO mpesa_collections VALUES ('12','Y5PDOPY','COMPLETE','M-PESA','0.00','12','12.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCG29MKHJU','','');
INSERT INTO mpesa_collections VALUES ('13','0L5DX9K','FAILED','M-PESA','0.00','31','31.00','254725887269','MFI-CUSTOMERS','','','Request cancelled by user','1032');
INSERT INTO mpesa_collections VALUES ('14','0WR49NK','FAILED','M-PESA','0.00','21','21.00','254725887269','MFI-CUSTOMERS','','','Request cancelled by user','1032');
INSERT INTO mpesa_collections VALUES ('15','04XZ450','COMPLETE','M-PESA','0.00','11','11.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCG89SD8KK','','');
INSERT INTO mpesa_collections VALUES ('16','08JVP60','COMPLETE','M-PESA','0.00','11','11.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCJ1I2B8D9','','');
INSERT INTO mpesa_collections VALUES ('17','08JVP60','COMPLETE','M-PESA','0.00','11','11.00','254725887269','MFI-CUSTOMERS','AVAILABLE','SCJ1I2B8D9','','');
DROP TABLE IF EXISTS mpesa_payments;
CREATE TABLE `mpesa_payments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `phone` mediumtext NOT NULL,
  `amount` mediumtext NOT NULL,
  `reason` mediumtext NOT NULL,
  `date` mediumtext NOT NULL,
  `trackingInfo` mediumtext NOT NULL,
  `status` mediumtext NOT NULL,
  `initiatedBy` mediumtext DEFAULT NULL,
  `reviewedBy` mediumtext DEFAULT NULL,
  `reviewedDate` mediumtext DEFAULT NULL,
  `approvedBy` mediumtext DEFAULT NULL,
  `approvedDate` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS mpesa_transfers;
CREATE TABLE `mpesa_transfers` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` mediumtext NOT NULL,
  `tracking_id` mediumtext NOT NULL,
  `status` mediumtext NOT NULL,
  `status_code` mediumtext NOT NULL,
  `transaction_id` mediumtext NOT NULL,
  `transaction_status` mediumtext NOT NULL,
  `transaction_status_code` mediumtext NOT NULL,
  `provider` mediumtext NOT NULL,
  `bank_code` mediumtext DEFAULT NULL,
  `name` mediumtext DEFAULT NULL,
  `account` mediumtext DEFAULT NULL,
  `account_type` mediumtext DEFAULT NULL,
  `account_reference` mediumtext DEFAULT NULL,
  `provider_reference` mediumtext DEFAULT NULL,
  `provider_account_name` mediumtext DEFAULT NULL,
  `amount` mediumtext NOT NULL,
  `charge` mediumtext DEFAULT NULL,
  `narrative` mediumtext DEFAULT NULL,
  `failed_amount` mediumtext DEFAULT NULL,
  `wallet_available_balance` mediumtext NOT NULL,
  `updated_at` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO mpesa_transfers VALUES ('1','YRXJ7RY','e67713f2-a459-4351-b929-7faf4cc5e88a','Completed','BC100','KJ5P8WK','Successful','TS100','MPESA-B2B','','MFI-SMS','969610','PayBill','LouriceFH','SCB2OEVT9A','  Advanta Africa Ltd','455.00','30.00','MFI-SMS BUY','0','1.74','2024-03-11T11:45:28.368809+03:00');
INSERT INTO mpesa_transfers VALUES ('2','0X3L6EY','82a8367d-bdc7-465c-aa07-fc558da47d7a','Completed','BC100','KQW3G3K','Successful','TS100','MPESA-B2B','','MFI-SMS','969610','PayBill','LouriceFH','SCB5OYCKMN','  Advanta Africa Ltd','8.00','30.00','MFI-SMS BUY','0','2.54','2024-03-11T14:24:11.583331+03:00');
INSERT INTO mpesa_transfers VALUES ('3','KOX3Z3K','2a511cbc-60e7-4d0a-8fe4-fcad762f9f64','Completed','BC100','Y9OX560','Successful','TS100','MPESA-B2B','','MFI-SMS','969610','PayBill','LouriceFH','SCB5OZ1V8V','  Advanta Africa Ltd','9.00','30.00','MFI-SMS BUY','0','3.31','2024-03-11T14:29:58.844070+03:00');
INSERT INTO mpesa_transfers VALUES ('4','YEVZ9GK','b53b3233-15e7-40b5-8bb2-7255fad648ef','Completed','BC100','Y7OVBEY','Successful','TS100','MPESA-B2B','','MFI-SMS','969610','PayBill','LouriceFH','SCG39FDBUZ','  Advanta Africa Ltd','10.00','30.00','MFI-SMS BUY','0','0.74','2024-03-16T20:25:19.546173+03:00');
DROP TABLE IF EXISTS notifications;
CREATE TABLE `notifications` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `message` mediumtext NOT NULL,
  `date` mediumtext NOT NULL,
  `level` mediumtext NOT NULL,
  `action` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO notifications VALUES ('1','ShtvLN6Kmz9c2n5rDYkS7+DmLKck1GaTHIwDMpeD1P+AQkJU0upDFJbOu7yDC5sRzLD1qKzEK+bdcNEYZQCw6m/6nP9K2+zvc2MEeMfiun60+NF5/KtyVKG6WSTk4lAvq0NvRBanYgsM4qTYCasQz8HdJomCkRF4ImnBF4JbAhU=','ShtvLN6Kmz9c2n5rDYkS7wOrL49C2UQXp2gUgLOsGDN5ApS4YxhaCbK6kKn53N8s0moVmaid9o1YhKDCFZ6U3FfKlVDp6dfghAYVjsi0IRKzaejJyKFpM27PSgx5SgV7mCny/iDs5JjF6fEJGj28Gg==','ShtvLN6Kmz9c2n5rDYkS7/SPmx6/2yYT3i9ATZ1tCAotj56FmxLQfdlhIUQizO6kvjaW0u8NoPwfykws5Vq4lq9y9JCMk/17Qo2XDGtdMonrTjn2fF9nGD+uSXhtFC5o','ShtvLN6Kmz9c2n5rDYkS78UdU4PRLecae40gu7Dcxakfz4AT5qjz3CZogf/kY7kNWzhfXVyQoproylZk1DwRrElym/aKrLE2oBAMGsPOaYlhY+wI+JqLdw1vuO4kE4gn');
INSERT INTO notifications VALUES ('2','ShtvLN6Kmz9c2n5rDYkS74JX7GM5OnVtOGUsYguoMITidSTKfBQtxbMW1aCBQNCVO71oR9ksOjo/7+dhzvC0MikQwrAENiACSyLfNwaJpqC0+NF5/KtyVKG6WSTk4lAvq0NvRBanYgsM4qTYCasQz48utdIxZxWE4jy9eVFxtZaX1FzT0FaPkRINHSYYM6BP','ShtvLN6Kmz9c2n5rDYkS7wF6tLe7rRgz2ttag2XKZINgGfGG5VUYSeg3pbNFVq3a4A6KYWaZDTiJ09bMBdg6Jg4i2sQSrhk1Uj5GuAAwLJ/NLWrwRoLS2kH3ocop/1ml0LOA2zVIL/GsT50nPtTItA==','ShtvLN6Kmz9c2n5rDYkS7/SPmx6/2yYT3i9ATZ1tCAotj56FmxLQfdlhIUQizO6kvjaW0u8NoPwfykws5Vq4lq9y9JCMk/17Qo2XDGtdMonrTjn2fF9nGD+uSXhtFC5o','ShtvLN6Kmz9c2n5rDYkS78UdU4PRLecae40gu7Dcxakfz4AT5qjz3CZogf/kY7kNWzhfXVyQoproylZk1DwRrElym/aKrLE2oBAMGsPOaYlhY+wI+JqLdw1vuO4kE4gn');
INSERT INTO notifications VALUES ('3','ShtvLN6Kmz9c2n5rDYkS7+DmLKck1GaTHIwDMpeD1P+AQkJU0upDFJbOu7yDC5sRzLD1qKzEK+bdcNEYZQCw6m/6nP9K2+zvc2MEeMfiun60+NF5/KtyVKG6WSTk4lAvq0NvRBanYgsM4qTYCasQz8HdJomCkRF4ImnBF4JbAhU=','ShtvLN6Kmz9c2n5rDYkS78n6i4y9r8EHoo2uSyHmsRU5yrTabSzE0aenD/ygO9Hhgj0lxcsqIlf/1mh88DkGeIqQ8qA/Kt4DXnH3TUtl+HaPc+qytQHtGNjgy68ONTe5NG34XAaVDfP/sbZzLk8ivg==','ShtvLN6Kmz9c2n5rDYkS7/SPmx6/2yYT3i9ATZ1tCAotj56FmxLQfdlhIUQizO6kvjaW0u8NoPwfykws5Vq4lq9y9JCMk/17Qo2XDGtdMonrTjn2fF9nGD+uSXhtFC5o','ShtvLN6Kmz9c2n5rDYkS78UdU4PRLecae40gu7Dcxakfz4AT5qjz3CZogf/kY7kNWzhfXVyQoproylZk1DwRrElym/aKrLE2oBAMGsPOaYlhY+wI+JqLdw1vuO4kE4gn');
INSERT INTO notifications VALUES ('4','ShtvLN6Kmz9c2n5rDYkS74JX7GM5OnVtOGUsYguoMITidSTKfBQtxbMW1aCBQNCVO71oR9ksOjo/7+dhzvC0MikQwrAENiACSyLfNwaJpqC0+NF5/KtyVKG6WSTk4lAvq0NvRBanYgsM4qTYCasQz48utdIxZxWE4jy9eVFxtZaX1FzT0FaPkRINHSYYM6BP','ShtvLN6Kmz9c2n5rDYkS70V0nc0BENVeVKgVpq90tR1k3Wug+FIlitfW40vql3Bth0CgmqLbD7QynaqXq/eqSQ3iBt/fbNY4aqMaA5BLdgyPc+qytQHtGNjgy68ONTe5g6YyxOTp6U++vcmp36nJ5g==','ShtvLN6Kmz9c2n5rDYkS7/SPmx6/2yYT3i9ATZ1tCAotj56FmxLQfdlhIUQizO6kvjaW0u8NoPwfykws5Vq4lq9y9JCMk/17Qo2XDGtdMonrTjn2fF9nGD+uSXhtFC5o','ShtvLN6Kmz9c2n5rDYkS78UdU4PRLecae40gu7Dcxakfz4AT5qjz3CZogf/kY7kNWzhfXVyQoproylZk1DwRrElym/aKrLE2oBAMGsPOaYlhY+wI+JqLdw1vuO4kE4gn');
INSERT INTO notifications VALUES ('5','ShtvLN6Kmz9c2n5rDYkS7+DmLKck1GaTHIwDMpeD1P+AQkJU0upDFJbOu7yDC5sRzLD1qKzEK+bdcNEYZQCw6m/6nP9K2+zvc2MEeMfiun60+NF5/KtyVKG6WSTk4lAvq0NvRBanYgsM4qTYCasQz8HdJomCkRF4ImnBF4JbAhU=','ShtvLN6Kmz9c2n5rDYkS70rWqntzKYOzKBEbCVOLYJ+sZf89qYZzM1u9qJQWvgUiNyX61QBJrpWOMlFqwl98KKwJSgOigk9db/098gOZIuEnK7dYjk0JJqpxnR7IzjLyJE8TWwX/jVQ2xxH0XSct7w==','ShtvLN6Kmz9c2n5rDYkS7/SPmx6/2yYT3i9ATZ1tCAotj56FmxLQfdlhIUQizO6kvjaW0u8NoPwfykws5Vq4lq9y9JCMk/17Qo2XDGtdMonrTjn2fF9nGD+uSXhtFC5o','ShtvLN6Kmz9c2n5rDYkS78UdU4PRLecae40gu7Dcxakfz4AT5qjz3CZogf/kY7kNWzhfXVyQoproylZk1DwRrElym/aKrLE2oBAMGsPOaYlhY+wI+JqLdw1vuO4kE4gn');
INSERT INTO notifications VALUES ('6','ShtvLN6Kmz9c2n5rDYkS74JX7GM5OnVtOGUsYguoMITidSTKfBQtxbMW1aCBQNCVO71oR9ksOjo/7+dhzvC0MikQwrAENiACSyLfNwaJpqC0+NF5/KtyVKG6WSTk4lAvq0NvRBanYgsM4qTYCasQz48utdIxZxWE4jy9eVFxtZaX1FzT0FaPkRINHSYYM6BP','ShtvLN6Kmz9c2n5rDYkS72rH1RDQgj08T8RMMySWPiH0dXRdP4Hu9SfyYhsNe53+pI6kdm/cnRiGASG3buZiWYVoTRluf5UEs6Pku5n3yOtNPfICNZq+uBEWMjHI3w6I6erqTr0nvnxlaH3eS7THdg==','ShtvLN6Kmz9c2n5rDYkS7/SPmx6/2yYT3i9ATZ1tCAotj56FmxLQfdlhIUQizO6kvjaW0u8NoPwfykws5Vq4lq9y9JCMk/17Qo2XDGtdMonrTjn2fF9nGD+uSXhtFC5o','ShtvLN6Kmz9c2n5rDYkS78UdU4PRLecae40gu7Dcxakfz4AT5qjz3CZogf/kY7kNWzhfXVyQoproylZk1DwRrElym/aKrLE2oBAMGsPOaYlhY+wI+JqLdw1vuO4kE4gn');
INSERT INTO notifications VALUES ('7','ShtvLN6Kmz9c2n5rDYkS7+DmLKck1GaTHIwDMpeD1P+AQkJU0upDFJbOu7yDC5sRzLD1qKzEK+bdcNEYZQCw6m/6nP9K2+zvc2MEeMfiun60+NF5/KtyVKG6WSTk4lAvq0NvRBanYgsM4qTYCasQz8HdJomCkRF4ImnBF4JbAhU=','ShtvLN6Kmz9c2n5rDYkS71Vj5lA9Jq0eb2o78wcSL+3DwSiK6ha5kiCHGAGw8r3t8zfTdXgZsJPRlnpEEHmXTkIjGQR+VSFmerTQqqUEa7r27Y7fWk+tJ+i3YOAJCW9Vg3vv8hA+79GZg/2FejO9Hw==','ShtvLN6Kmz9c2n5rDYkS7/SPmx6/2yYT3i9ATZ1tCAotj56FmxLQfdlhIUQizO6kvjaW0u8NoPwfykws5Vq4lq9y9JCMk/17Qo2XDGtdMonrTjn2fF9nGD+uSXhtFC5o','ShtvLN6Kmz9c2n5rDYkS78UdU4PRLecae40gu7Dcxakfz4AT5qjz3CZogf/kY7kNWzhfXVyQoproylZk1DwRrElym/aKrLE2oBAMGsPOaYlhY+wI+JqLdw1vuO4kE4gn');
INSERT INTO notifications VALUES ('8','ShtvLN6Kmz9c2n5rDYkS74JX7GM5OnVtOGUsYguoMITidSTKfBQtxbMW1aCBQNCVO71oR9ksOjo/7+dhzvC0MikQwrAENiACSyLfNwaJpqC0+NF5/KtyVKG6WSTk4lAvq0NvRBanYgsM4qTYCasQz48utdIxZxWE4jy9eVFxtZaX1FzT0FaPkRINHSYYM6BP','ShtvLN6Kmz9c2n5rDYkS7zBTKyFvyWE0llOwdmSVCoT2pmssH8+MNlMbqMp6oCCJAybV091fdrAO7CiCtC0VRbp5UxfWZEhloFS4oTKU52f27Y7fWk+tJ+i3YOAJCW9Vdss+Ixp9yIS4RX3SpaCobA==','ShtvLN6Kmz9c2n5rDYkS7/SPmx6/2yYT3i9ATZ1tCAotj56FmxLQfdlhIUQizO6kvjaW0u8NoPwfykws5Vq4lq9y9JCMk/17Qo2XDGtdMonrTjn2fF9nGD+uSXhtFC5o','ShtvLN6Kmz9c2n5rDYkS78UdU4PRLecae40gu7Dcxakfz4AT5qjz3CZogf/kY7kNWzhfXVyQoproylZk1DwRrElym/aKrLE2oBAMGsPOaYlhY+wI+JqLdw1vuO4kE4gn');
DROP TABLE IF EXISTS offers;
CREATE TABLE `offers` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `offer_name` mediumtext NOT NULL,
  `offer_image_poster` mediumtext NOT NULL,
  `start_date` mediumtext NOT NULL,
  `end_date` mediumtext NOT NULL,
  `status` mediumtext NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS orders;
CREATE TABLE `orders` (
  `order_number` int(11) NOT NULL AUTO_INCREMENT,
  `orderTime` mediumtext NOT NULL,
  `custName` mediumtext NOT NULL,
  `email` mediumtext NOT NULL,
  `phone` mediumtext NOT NULL,
  `product` mediumtext NOT NULL,
  `quantity` mediumtext NOT NULL,
  `delivered` mediumtext DEFAULT NULL,
  `country` mediumtext NOT NULL,
  `postal_address` mediumtext NOT NULL,
  `postal_code` mediumtext NOT NULL,
  `location` mediumtext NOT NULL,
  PRIMARY KEY (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS otpQ;
CREATE TABLE `otpQ` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `phone` mediumtext NOT NULL,
  `otpHash` mediumtext NOT NULL,
  `dateInitiated` mediumtext NOT NULL,
  `status` mediumtext DEFAULT NULL,
  `dateDelivered` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS payments;
CREATE TABLE `payments` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `custID` mediumtext DEFAULT NULL,
  `name` mediumtext NOT NULL,
  `phone` mediumtext NOT NULL,
  `services` mediumtext NOT NULL,
  `amount` mediumtext NOT NULL,
  `staff_name` mediumtext NOT NULL,
  `staff_phone` mediumtext NOT NULL,
  `date` mediumtext NOT NULL,
  `commission_paid` mediumtext NOT NULL DEFAULT 'Not Paid',
  `payment_mode` mediumtext NOT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS performance;
CREATE TABLE `performance` (
  `location_name` mediumtext DEFAULT NULL,
  `cashIn` mediumtext DEFAULT NULL,
  `cashOut` mediumtext DEFAULT NULL,
  `income` mediumtext DEFAULT NULL,
  `percent` mediumtext DEFAULT NULL,
  `date` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS performanceHistory;
CREATE TABLE `performanceHistory` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `cashIn` mediumtext DEFAULT NULL,
  `cashInCount` mediumtext DEFAULT NULL,
  `cashOut` mediumtext DEFAULT NULL,
  `cashOutCount` mediumtext DEFAULT NULL,
  `income` mediumtext DEFAULT NULL,
  `percent` mediumtext DEFAULT NULL,
  `date` mediumtext DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  `month` mediumtext DEFAULT NULL,
  `year` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS recruit;
CREATE TABLE `recruit` (
  `staff_no` int(11) NOT NULL AUTO_INCREMENT,
  `staff_name` mediumtext NOT NULL,
  `staff_phone` mediumtext NOT NULL,
  `staff_email` mediumtext NOT NULL,
  `joinDate` mediumtext NOT NULL,
  `skills` mediumtext NOT NULL,
  `cv` mediumtext DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`staff_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS recurrentExp;
CREATE TABLE `recurrentExp` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` mediumtext NOT NULL,
  `amount` mediumtext NOT NULL,
  `date` mediumtext NOT NULL,
  `currentTotal` mediumtext NOT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS sentSMS;
CREATE TABLE `sentSMS` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` mediumtext NOT NULL,
  `message` mediumtext NOT NULL,
  `date` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS signup;
CREATE TABLE `signup` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `bizname` mediumtext NOT NULL,
  `email` mediumtext NOT NULL,
  `phone` mediumtext NOT NULL,
  `code` mediumtext NOT NULL,
  `username` mediumtext NOT NULL DEFAULT 'demo',
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS smsQ;
CREATE TABLE `smsQ` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` mediumtext NOT NULL,
  `message` mediumtext NOT NULL,
  `sender1` mediumtext NOT NULL,
  `sender2` mediumtext NOT NULL,
  `dateInitiated` mediumtext NOT NULL,
  `dateDelivered` mediumtext DEFAULT NULL,
  `status` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS staff;
CREATE TABLE `staff` (
  `staff_no` int(11) NOT NULL AUTO_INCREMENT,
  `staff_name` mediumtext NOT NULL,
  `staff_phone` mediumtext NOT NULL,
  `staff_email` mediumtext NOT NULL,
  `rate` mediumtext DEFAULT NULL,
  `joinDate` mediumtext NOT NULL,
  `ID_front` mediumtext NOT NULL,
  `ID_back` mediumtext NOT NULL,
  `passport_pic` mediumtext NOT NULL,
  `contract` mediumtext NOT NULL,
  `status` mediumtext DEFAULT NULL,
  `role` mediumtext DEFAULT NULL,
  `exit_comment` mediumtext DEFAULT NULL,
  `exited_date` mediumtext DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`staff_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO staff VALUES ('1','ShtvLN6Kmz9c2n5rDYkS7861R/GDd/p630tMr4iGptL1axLuKMHUuI/SyIv+Hv4g0J83Ml2amaxX/iGgStSYoYlqprblbDeKn8aoK1X8z0EhC+dzv4SnjNaZU/5XW1PR','ShtvLN6Kmz9c2n5rDYkS78db4bXU1aDT8JjTUXZYMBnuzwHt8a1isLzUTrMvpw/V4fiB+tbdJt1vdpeuvDblN3Pj3DGFe2kT0Kqgc2q0Gx+Lsmd07Oqi20xj9a6oIvMR','ShtvLN6Kmz9c2n5rDYkS7xYWOVktiACK3CuY2/F8yHYJpR/dCiEnHStVqcxSZOl6PM4uG2wcV7mcMqPfwfeFmq3uvQf103ddUVYzKoeMDQtX7LU9wurUsX6aJ7oBdOny3zmfGN3xI8ySENbBB1bmtQ==','ShtvLN6Kmz9c2n5rDYkS7wyFf0UXcgzwDoqv6LZZDtv1XT5up5GYM0iubnwSSyWevTsEKutPSQB1HT8nFYfIDFanoKBHiBci/R5iUXbHrsY5YBM0iNtgHSmkfImbt5Uv','ShtvLN6Kmz9c2n5rDYkS7wnxYSyoBzxXB/0iFZKiiknKjswWwC3fv9GsgmBT8LkzPsAFFhF9/S+4tFKUaOYvtY4Ic8Ra3Ad3q3A2B7iaoYzTg9UuARe9WtdjXyAy7QRA','ShtvLN6Kmz9c2n5rDYkS7/4faTsrNhgwznvY3cu51YtUzVX0pGRF9rCPNoZpjKPegM9smICOYbzzPbwWUGjzcFnP1IiqAEjiYr6SaYYZpr/6ogvVMprhLC/rOmYE+zyG4PNX07orixqmWaaciThl0QCxc4h7uh8r8WKVdQkETr4=','ShtvLN6Kmz9c2n5rDYkS70t42imDizC1UsJuJ9X8Jiek4AdE2DaXi/nmAdAJtPidiWcxsis8W4UpDB9+D/5VmpyHYm6/N3TyywtP5e0+6YL6ogvVMprhLC/rOmYE+zyGSSPCNCFHJAqf8TDq1RD4+oQS84W02LCbRfw2y0JajtA=','ShtvLN6Kmz9c2n5rDYkS72bcIQlJpyOGJ/G5X8fMwgqjh/BAD+UKSaPk7C6f4GhSKRPZ8M0GIByUzYibtNBgAq2IYehII7sX0baOanHmAZv6ogvVMprhLC/rOmYE+zyG/FxmJ+P3JQOyAgz/cKwKLzKJdq3HHNjLUpCVv7kMDjunNGNDBjjIL+J6dcAf9hG3','ShtvLN6Kmz9c2n5rDYkS7xOlTOKLL1sa38M1IF4dF4hn2EIZvMQ0CqFww9iSWf46Wpp4p9s5lViIMDUwZSHwla94Wjkj9e6ElJK05xwF8N/6ogvVMprhLC/rOmYE+zyGi203sXvVtvXzumViyIrRtSttleMHUATmyGEQWYUlIPI=','ShtvLN6Kmz9c2n5rDYkS79pgqR5FQ068SKGSN8Qt4d+nuvykanvidfH/7v0wZXe37j3UIR+9wubJFPoICaLJ8j2MVVvvVUBDIyTlGCuywSAqynCd3RnR8Imhp2/Q+03o','ShtvLN6Kmz9c2n5rDYkS7xk8cbUmG9+5kDAM6BuzyAD79fGU6YyYujV46/BqWAJbENNXAy+2+PepiQKXhJmZrPQFLnuy9cYYgzZRjYmwnBYoBKGxQQnoDLt5yI20S6Iu','ShtvLN6Kmz9c2n5rDYkS79D4RpRVyxsDrLgKUbi3VNpsu0avO2wKMI1s50+cUVh3hvBj+o6MvKuG68NmPXUSE4RZkBg4kEsr6cCQApYc2EhO/xd9uXpwOV9tM/MK8k+v','ShtvLN6Kmz9c2n5rDYkS76UBhCilsDrk90ZwHBPEGLU1Y2mZe2/6UyHFeazRaFtAy8kTyUnfHQWP9gmH3/DA7n0XDDDR1NKOZLBkGs9jRmZJa798RBKyUzUVJDxPJDO3LFahmvGNdHcZZ9s8SIEzjA==','Machakos');
DROP TABLE IF EXISTS support_tickets;
CREATE TABLE `support_tickets` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `ticketID` mediumtext DEFAULT NULL,
  `issue` mediumtext NOT NULL,
  `type` mediumtext NOT NULL,
  `ticket_by` mediumtext NOT NULL,
  `ticket_date` mediumtext NOT NULL,
  `action` mediumtext DEFAULT NULL,
  `comments` mediumtext DEFAULT NULL,
  `action_by` mediumtext DEFAULT NULL,
  `action_date` mediumtext DEFAULT NULL,
  `status` mediumtext DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS target;
CREATE TABLE `target` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `location_name` mediumtext DEFAULT NULL,
  `monthlyTarget` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
DROP TABLE IF EXISTS userlogs;
CREATE TABLE `userlogs` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `username` mediumtext NOT NULL,
  `user_activity` mediumtext DEFAULT NULL,
  `date` mediumtext NOT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO userlogs VALUES ('1','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS78s2BQIUEA1G3FfE6rrntVNCJlNWgwQGYmOSxmlQMFrGiw5kT7YhhuGWeqEF8p3Dy9rSUQ8rQLdSlqzwTh7Akhl+o+ZcGCwHhJ2pXG5fuRUxu8YMFm2XAgfTSsHpveAplA==');
INSERT INTO userlogs VALUES ('2','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS71za3N69SGf/imX13oJvu7J/k6FBk925/BhmkqBFI3+9U1LbU9VEbouRl5xtlk5bqOArm0ygCK5cyCyD1buyoFYauxBl/j2TaIixemIPPw80','ShtvLN6Kmz9c2n5rDYkS73tb1YXFI0bkqtFAXZqGW/7ZCsF1z89j4T2PY537z2auLzOrBWTclvprOuDXFgYjKSFM+QvABgzJQ6b3y+IQKTp0weTDH7HfHg6gVAVL0jFM6adiV2YD0BmkmlWeH+oi6w==');
INSERT INTO userlogs VALUES ('3','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS71za3N69SGf/imX13oJvu7J/k6FBk925/BhmkqBFI3+9U1LbU9VEbouRl5xtlk5bqOArm0ygCK5cyCyD1buyoFYauxBl/j2TaIixemIPPw80','ShtvLN6Kmz9c2n5rDYkS72NV5/m7lrJPt3aGX8LqmOvE9wD+cExS2vM0Y2xYw3/6qfFOis4+t0ddC2lxGYvsR5AT3EtbGTo958cM287ZiksN0ARs2Wq0mNN+Poqe9s0cTZU4W8zRzeRVlvGhmC2r7w==');
INSERT INTO userlogs VALUES ('4','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS71za3N69SGf/imX13oJvu7J/k6FBk925/BhmkqBFI3+9U1LbU9VEbouRl5xtlk5bqOArm0ygCK5cyCyD1buyoFYauxBl/j2TaIixemIPPw80','ShtvLN6Kmz9c2n5rDYkS7zMFkeYaj1n59vtYExHpgxn6w2WMX2/b3WTju6JFT3+/YkM/qcr3GImjOLEEHCgKshcjVD9G720o3vSX22eFnlHqwokHjOnuk2x6CpG16lnyjuu7qAJQcrXVuXAkPbn56w==');
INSERT INTO userlogs VALUES ('5','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS71za3N69SGf/imX13oJvu7J/k6FBk925/BhmkqBFI3+9U1LbU9VEbouRl5xtlk5bqOArm0ygCK5cyCyD1buyoFYauxBl/j2TaIixemIPPw80','ShtvLN6Kmz9c2n5rDYkS70staJY5nCeNPhftkeqktxQfwa0b7v0xCNGEIBTMmSLDpCp3CIr9pcE5qnU1ldCXJNpboQiiASO3sMzEYQaBLj+o+KN8B/CkvEFRAMM2tQzyXDhMxRrMIuS9Y8+lvzWRlA==');
INSERT INTO userlogs VALUES ('6','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS70TWhU8FdbkB6QK8UzeiNxFN+oi4g9zZbOdxO4mHeHDH8vvoH9tynCVfGSM6txn905uJZ02KUW70Rg5VQe2wGrnor3l7p7hJWuykBeOfzjkenzzuN/wDNEu7QJuGXdoEfQ==');
INSERT INTO userlogs VALUES ('7','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS797G/bqfQZ1JgOt+AXzqKrktlW1pdCUF40j2SwRfYGImMnv9gSqHx+wpaSjXzS0uE/4J4zPzt3rAsI4HQ2Oa/uBNbHvvlObJf8avD++fdhu6WS5bycAGn4JSrDlenZ5gEw==','ShtvLN6Kmz9c2n5rDYkS7968U/AL7BXZJRiQYvL27i2S40dx+JXA9dif0Tt2+VsSZ2vbsmQGL6YhRFIjkiqQXCd8E9nrodGh+vXM0xpM2i1Ja798RBKyUzUVJDxPJDO3TBCcvgZT5sNtwQWaDlVfgQ==');
INSERT INTO userlogs VALUES ('8','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7wt42FKK72ckx22bVHQAJbwIWW0XkH3a6TpmhvTcoNUORhmq9PGkC3ioWbbN0M8u12n8NuvlAUA6FalS8Dujx00YzJOgFFPeny6jCmdY5O+CUzHa6w1oNID+k1z8+3DOu690lcmTHakvT9wcoD6CILY=','ShtvLN6Kmz9c2n5rDYkS73YaEZKiLQs+VKppZMsxkgkx6MzpM5Jn2Qg8F+rrmYYxPLB3AwIqVETimH6Ddt8kShU+Bj+pXR4BWRswShY5ke1Ja798RBKyUzUVJDxPJDO3TBcjbgFoB8TaDF1oE6aHEg==');
INSERT INTO userlogs VALUES ('9','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS74+MtfisKfhK1z0fLuffvMb5gwVcgMl1bhrxI3VOos5YvCTx93/zEQuxmPFN5c9Al5Nv/7PjxhBbJD/QoNn0hJXC2HQwaWDKIhiQ+VVc9xUTTzA5InyN3AI+eLQeqFbrLQ==','ShtvLN6Kmz9c2n5rDYkS76UBhCilsDrk90ZwHBPEGLU1Y2mZe2/6UyHFeazRaFtAy8kTyUnfHQWP9gmH3/DA7n0XDDDR1NKOZLBkGs9jRmZJa798RBKyUzUVJDxPJDO3LFahmvGNdHcZZ9s8SIEzjA==');
INSERT INTO userlogs VALUES ('10','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7y+BZwuKY/v/CNFaKDU7v8FCexe0pOXxNZikkd9bw46OESvhekhMOc4xqcNPT6BiOZ6cylrLnA9WxnQwiRjvoIgx8LGO36AUkhbLatornI3N61hZJr2n7ARVzwlfIj024w==','ShtvLN6Kmz9c2n5rDYkS7/32Pd8vDaNKyx3Tm7OHEDpvt4eqwMrt/hC01CmHZzdimrbdrNrgmnEEiEmRfYyagUlJ7R4kuvCdffKhBY1UeH5Ja798RBKyUzUVJDxPJDO3EUeymvtBjXC6SVW4CpgCgA==');
INSERT INTO userlogs VALUES ('11','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS79Am5kRzydKnd9a8lpGTq2T4DznHF7ikO7szg6Dw6U1+nqd2n1CWTtIHLlDEctVmb7DxS/m7NKIZNru98Xm7JhqxMPXAUCwPgqMCtJyLbq6LumCUjYi3FCxO9ieGpGc1aA==');
INSERT INTO userlogs VALUES ('12','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS76V514iwBPXn7DiEyIX8bsTc057/vbnlgYDzmEyabIkrcQ5VN5bPZD/ThmjJZOkfoDLin6n1rUjpmzuQAjte1t2Zfg1Zsr4te3bPN2xkgoigfmcwXWljJuIKyd4nTqnYdw==');
INSERT INTO userlogs VALUES ('13','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS7xzekXxU6iLfWMAzJA/27SIL9GYycGF4zVOl6tXolMOpsuegPQL3mTRYfM6REhm5DYYbTpl+DHW5PzgtPuEmYsyYp+kHtQDPe5sfV0yn1E0AyqJ/srbfdz+NMRb7uWJDjw==');
INSERT INTO userlogs VALUES ('14','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS78ReOm0wDav8Cz0iV46wp3+zGlm/2RxfPh35ZAR2CaEn4eK4EgkLc/QQUwDCGhWMJ5EG5rCMZJFTepLhCTbzdM67mC1sVa7dAjgAvuoz+aP4n6y443fbW8zuGQOUZEkfhg==');
INSERT INTO userlogs VALUES ('15','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS7w69EhAmEeDLr2vFRp5JnKgiLyvPGvcrA+B9EYQp41P1qK0/nvDDK8Wv23bGBxo0TSChgsL3ZuyjdWbH1BMi3CfpUHLacTdNBibsVI7X4WQYaaxsJxf11Xfl3/VG2y0WRA==');
INSERT INTO userlogs VALUES ('16','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS735qHKShNiIQVeFe67QUi5zbiGuIXYXSmdAzdwLOyWBAHO+xKV9ZDMW1T+C4bALYm5o9Fbi/fgZQXjWq2IVlTxiar+1CZqykgfAT/MN6DYTce/3BtBNwy1wml362jIqTEw==');
INSERT INTO userlogs VALUES ('17','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS7+uTEeQH7mZo7fya5mzJEQfvhMiKNoxgvyQysYVus6EAaCSm0a9um1U++Vq6ANcOvN4avgow6CNRBFtBOXWbw/AasyNNLDrKVKbgaPiHOhpwmLOgXfmU0rzKJfkEK6GdEg==');
INSERT INTO userlogs VALUES ('18','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS7xFRuJGE/mJ7+TahIZHsOfbouh9Pm0OLCzplC10EYxAuPtZObZePwOGCpi9f0xeQcnz1/gS6DVlRJuaNbHTV4Dk1Ip4ckAeKYpfEKx+3hZQGWOhJF3NwmzNyz/v77BaY1Q==');
INSERT INTO userlogs VALUES ('19','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS71bXJMKpKSPTVropUa9Q9z3TguStiXElDjEkfSPGwwl1Otzu5H7CmPQZUz3mS4dJbbYLOphrrb/cmCsaM84ZqeXSxNT/rFBwKCjb2lWrO1x2PJxphr5BDn2jCHJyTC83zA==');
INSERT INTO userlogs VALUES ('20','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS778cdnG1e8NZt9FGGU1/CjPZ5xN6DVysK/8H7PnZUD50BPiO2hdZ7kxuETRpvv5ogtqVPF8i8+JmS53e845BlwDOsYmrdQbLwsn3y6IyR3MQl/wp3bbOcoXjpHDy9K5Umg==');
INSERT INTO userlogs VALUES ('21','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS77m8UNakeJkFaV3sxZhESsneARN7lFt32BQQu0o9Go3SGxWiOESvKe/zUzz510q/4yyvATxORxeOLLKpg5gWW6ot2PP7qcad5o4ZofKs4f39luc3cQ49Kc1R0aVhOC0pLg==');
INSERT INTO userlogs VALUES ('22','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS7/aNlKM4dDJ7e9oNemEyFAUfD99cAweg7QMvGw/L476QwxN4gBMqFJEF0Gw0+GPUgA4xf3apxwXOCswrIeBNbL7+9jhI1oKSUP8tnPSAHsZ49v9Sk63QZliiJN9eccychw==');
INSERT INTO userlogs VALUES ('23','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS74nK33eyKxdKXtpR3zFMt24wbXx3sDdTIl1Ok7Xgoo89jNlTYXNfGTGOq7vKSGIw8qkZfXoMvAEeEzf1EjN2VfSoDF+YBQqITalGztLlMr2t6Axu+5FmfM/o78JPy85bnQ==');
INSERT INTO userlogs VALUES ('24','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS72/T01iDYwmR8X0hBMJuFauh4xjcWpuio6YfYEFW4DcMko0EfG0nVfK2Jhw1F+OFhgrT3n86YkMZcd7ZD4OWk4bECQxXEc0XxBOeXdKBj2QSu7OuedKtczO5qzoeD7/06Q==');
INSERT INTO userlogs VALUES ('25','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS7xPKZI70sGYroswBRG6zziDmJ4cVuMXX0/DMmo/SRHnAiergxAQPuZhjEXxDzjJWqCR40/6zCNavOaUwM6MrK40mCoOwuOImy2+Lxv2IJYLx','ShtvLN6Kmz9c2n5rDYkS76y8oeKC6kb2TKoXb2+rlKgYqub4cuiNKQEEgGujjj6fmVxUR8TyGdYISGOEZzHBySoOQluSG4TC3F3/0KGfAhoFxsiPFaXFKe8k/TQfhBNQZ9p8HlaV1LQ59POFsZZbFw==');
DROP TABLE IF EXISTS users;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_no` mediumtext NOT NULL,
  `username` mediumtext NOT NULL,
  `password` mediumtext DEFAULT NULL,
  `email` mediumtext NOT NULL,
  `phone` mediumtext NOT NULL,
  `token` mediumtext DEFAULT NULL,
  `lastResetDate` mediumtext DEFAULT NULL,
  `api_key` mediumtext DEFAULT NULL,
  `role` mediumtext DEFAULT NULL,
  `custID` mediumtext DEFAULT NULL,
  `transaction_limit` mediumtext DEFAULT NULL,
  `location_name` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO users VALUES ('1','0','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS77oUNjhB7ZXUVGhrr8lBOAgxhK+GSzaTf7Q3bQ9po5bKlIvv6G9mrsysQo/YxHdCOOVewJiMH3TVZfwyEH66zwQK0dYZzUm/rrwDSeMJxcZo','ShtvLN6Kmz9c2n5rDYkS71s7GjskjDACWH1SWqSGcCYGI4E7hI8NvGEm15YtkL9YTXn7oUA38AxEvxO+wverOKs8JWqyuRwkWCEc4LZO3VyBFNyousgK1E6qn2IFkBgjJgcl+yQgOzpew4kG284IyQ==','ShtvLN6Kmz9c2n5rDYkS73zdq/O7N7X0vKnln4PQjsrFb/Zm+jdhb+zwiUQ8c+0JaGZTwpDUGhVJjxUu1aVsOs5r1IYKJyAgzJIGqZ6m0Dk0dqQq8+Ub/o+16jDTwmvQ','','ShtvLN6Kmz9c2n5rDYkS79h4EWpxZZF6MTSbSmrReOE/gazeJ/k7F87xtHCkIiUCThNyRr1W3Vp65LDH0zs55wqv1SYs4k3HFoVfoC7BHA7egVEsje0zulwLV42JAq+lOHIZnrOCkr25v9vfbu+3gA==','','ShtvLN6Kmz9c2n5rDYkS72FgX6rjUJfHuqbDgsr3M8duEl0KT0rvOFtqG9HmJbpqZM5M7NncomId8IKwWY1Zqvoxi2JZpa+uhYS1OYzHmNhFGSdKckr7IYwpgpL8Ebki','2404','','');
DROP TABLE IF EXISTS wallet;
CREATE TABLE `wallet` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `mpesa` mediumtext DEFAULT NULL,
  `kcb` mediumtext DEFAULT NULL,
  `payroll` mediumtext DEFAULT NULL,
  `sms` text DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
INSERT INTO wallet VALUES ('1','0','0','0','117');
