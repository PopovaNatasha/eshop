CREATE TABLE product_tag
(
	PRODUCT_ID int not null,
	TAG_ID int not null,
	PRIMARY KEY (PRODUCT_ID, TAG_ID),
	FOREIGN KEY FK_PT_PRODUCT (PRODUCT_ID)
		REFERENCES product(ID)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT,
	FOREIGN KEY FK_PT_TAG (TAG_ID)
		REFERENCES tag(ID)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT
);

CREATE TABLE product_order
(
	PRODUCT_ID int not null,
	ORDER_ID int not null,
	COUNT int not null,
	PRICE DOUBLE not null,
	PRIMARY KEY (PRODUCT_ID, ORDER_ID),
	FOREIGN KEY FK_PO_PRODUCT (PRODUCT_ID)
		REFERENCES product(ID)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT,
	FOREIGN KEY FK_PO_ORDER (ORDER_ID)
		REFERENCES `order`(ID)
		ON UPDATE RESTRICT
		ON DELETE RESTRICT
);