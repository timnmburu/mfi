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
