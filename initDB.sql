/* Database for use with DIS-COMP4039 Coursework 2 
 * 
 * Please note you do not have to use this.  If you find
 * it easier to use a database of your own design then
 * you are free to do so.  
 *
 * If you do use this database, use it as a starting point only.
 * You will not be able to complete the coursework without 
 * modifying it to some extent.
 */

DROP TABLE IF EXISTS Fines;
DROP TABLE IF EXISTS Incident;
DROP TABLE IF EXISTS Offence;
DROP TABLE IF EXISTS Ownership;
DROP TABLE IF EXISTS People;
DROP TABLE IF EXISTS Vehicles;
DROP TABLE IF EXISTS Accounts;


DROP TABLE IF EXISTS Accounts;
CREATE TABLE Accounts (
  Account_username varchar(40) NOT NULL,
  Account_password varchar(40) NOT NULL,
  Account_userType varchar(10) NOT NULL,
  Officer_name varchar(40) NOT NULL,
  Officer_ID varchar(20) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO Accounts (Account_username, Account_password, Account_userType, Officer_name, Officer_ID) VALUES
("mcnulty", "plod123", "police", "Mike Nulty", "mk001"),
("moreland", "fuzz42", "police", "More Land", "ml001"),
("daniels", "copper99", "admin", "Daniel Sull", "ds001");

ALTER TABLE Accounts
  ADD PRIMARY KEY (Account_username),
  ADD CHECK(Account_userType IN ("police","admin"));







DROP TABLE IF EXISTS Vehicles;
CREATE TABLE Vehicles (
  Vehicle_ID int(20) NOT NULL,
  Vehicle_make varchar(20) DEFAULT NULL,
  Vehicle_model varchar(20) DEFAULT NULL,
  Vehicle_colour varchar(20) DEFAULT NULL,
  Vehicle_licence varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE Vehicles
  ADD PRIMARY KEY (Vehicle_ID);

INSERT INTO Vehicles (Vehicle_ID, Vehicle_make, Vehicle_model, Vehicle_colour, Vehicle_licence) VALUES
  (12, 'Ford', 'Fiesta', 'Blue', 'LB15AJL'),
  (13, 'Ferrari', '458', 'Red', 'MY64PRE'),
  (14, 'Vauxhall', 'Astra', 'Silver', 'FD65WPQ'),
  (15, 'Honda', 'Civic', 'Green', 'FJ17AUG'),
  (16, 'Toyota', 'Prius', 'Silver', 'FP16KKE'),
  (17, 'Ford', 'Mondeo', 'Black', 'FP66KLM'),
  (18, 'Ford', 'Focus', 'White', 'DJ14SLE'),
  (19, 'Tesla', 'Model3', 'White', 'TE12SLA'),
  (20, 'Nissan', 'Pulsar', 'Red', 'NY64KWD'),
  (21, 'Renault', 'Scenic', 'Silver', 'BC16OEA'),
  (22, 'Hyundai', 'i30', 'Grey', 'AD223NG');

ALTER TABLE Vehicles
  MODIFY Vehicle_ID int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;







DROP TABLE IF EXISTS People;
CREATE TABLE People (
  People_ID int(20) NOT NULL,
  People_name varchar(50) NOT NULL,
  People_address varchar(50) DEFAULT NULL,
  People_licence varchar(16) UNIQUE DEFAULT NULL,
  People_DOB date DEFAULT NULL,
  People_photoID int(20) DEFAULT NULL -- prefix of file name
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE People
  ADD PRIMARY KEY (People_ID);

INSERT INTO People (People_ID, People_name, People_address, People_licence, People_DOB, People_photoID) VALUES
(1, 'James Smith', '23 Barnsdale Road, Leicester', 'SMITH92LDOFJJ829', '1991-02-12', NULL),
(2, 'Jennifer Allen', '46 Bramcote Drive, Nottingham', 'ALLEN88K23KLR9B3', '1994-03-12', NULL),
(3, 'John Myers', '323 Derby Road, Nottingham', 'MYERS99JDW8REWL3', '1981-04-25', NULL),
(4, 'James Smith', '26 Devonshire Avenue, Nottingham', 'SMITHR004JFS20TR', '1978-11-24', NULL),
(5, 'Terry Brown', '7 Clarke Rd, Nottingham', 'BROWND3PJJ39DLFG', '1995-06-14', NULL),
(6, 'Mary Adams', '38 Thurman St, Nottingham', 'ADAMSH9O3JRHH107', '1996-03-11', NULL),
(7, 'Neil Becker', '6 Fairfax Close, Nottingham', 'BECKE88UPR840F9R', '1988-11-22', NULL),
(8, 'Angela Smith', '30 Avenue Road, Grantham', 'SMITH222LE9FJ5DS', '1953-09-22', NULL),
(9, 'Xene Medora', '22 House Drive, West Bridgford', 'MEDORH914ANBB223', '1969-07-22', NULL),
(10, 'Smith Tony', '22 Avenue Road, Grantham', NULL, '2012-01-01', NULL);

ALTER TABLE People
  MODIFY People_ID int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;







DROP TABLE IF EXISTS Ownership;
CREATE TABLE Ownership (
  Ownership_ID int(21) NOT NULL,
  People_ID int(20) DEFAULT NULL,
  Vehicle_ID int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE Ownership
  ADD PRIMARY KEY (Ownership_ID),
  ADD KEY fk_people (People_ID),
  ADD KEY fk_vehicles (Vehicle_ID);

INSERT INTO Ownership (Ownership_ID, People_ID, Vehicle_ID) VALUES
(1, 3, 12),
(2, 8, 20),
(3, 4, 15),
(4, 4, 13),
(5, 1, 16),
(6, 2, 14),
(7, 5, 17),
(8, 6, 18),
(9, 7, 21),
(10, NULL, 22);
-- if put more ownership in initial db, update the test case of testInsertOwnershipBothExisted in Vehicles/_ownership.php/testInsertOwnershipBothExisted_Trivial()
ALTER TABLE Ownership
  MODIFY Ownership_ID int(21) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE Ownership
  ADD CONSTRAINT fk_people FOREIGN KEY (People_ID) REFERENCES People (People_ID),
  ADD CONSTRAINT fk_vehicles FOREIGN KEY (Vehicle_ID) REFERENCES Vehicles (Vehicle_ID);








DROP TABLE IF EXISTS Offence;
CREATE TABLE Offence (
  Offence_ID int(11) NOT NULL,
  Offence_description varchar(50) NOT NULL,
  Offence_maxFine int(11) NOT NULL,
  Offence_maxPoints int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE Offence
  ADD PRIMARY KEY (Offence_ID);

INSERT INTO Offence (Offence_ID, Offence_description, Offence_maxFine, Offence_maxPoints) VALUES
(1, 'Speeding', 1000, 3),
(2, 'Speeding on a motorway', 2500, 6),
(3, 'Seat belt offence', 500, 0),
(4, 'Illegal parking', 500, 0),
(5, 'Drink driving', 10000, 11),
(6, 'Driving without a licence', 10000, 0),
(7, 'Driving without a licence', 10000, 0),
(8, 'Traffic light offences', 1000, 3),
(9, 'Cycling on pavement', 500, 0),
(10, 'Failure to have control of vehicle', 1000, 3),
(11, 'Dangerous driving', 1000, 11),
(12, 'Careless driving', 5000, 6),
(13, 'Dangerous cycling', 2500, 0);

ALTER TABLE Offence
  MODIFY Offence_ID int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;








DROP TABLE IF EXISTS Incident;
CREATE TABLE Incident (
  Incident_ID int(15) NOT NULL,
  Ownership_ID int(21) DEFAULT NULL, -- foriegn key (Ownership) owner of the vehicles involved in the incident is here
  People_ID int(20) DEFAULT NULL, -- foriegn key (People) the one who break the law
  Offence_ID int(11) NOT NULL, -- foriegn key (Offence)
  Account_username varchar(40) NOT NULL, -- foriegn key (Account)
  Incident_date date NOT NULL,
  Incident_report varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE Incident
  ADD PRIMARY KEY (Incident_ID);

INSERT INTO Incident (Incident_ID, Ownership_ID, People_ID, Offence_ID, Account_username, Incident_Date, Incident_Report) VALUES
(1, 3, 4, 1, "daniels", '2017-12-01', '40mph in a 30 limit'),
(2, 2, 8, 4, "moreland", '2017-11-01', 'Double parked'),
(3, 4, 4, 1, "mcnulty", '2017-09-17', '110mph on motorway'),
(4, 6, 2, 8, "moreland", '2017-08-22', 'Failure to stop at a red light - travelling 25mph'),
(5, 4, 4, 3, "daniels", '2017-10-17', 'Not wearing a seatbelt on the M1');

ALTER TABLE Incident
  MODIFY Incident_ID int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6,
  ADD CONSTRAINT fk_incident_offence FOREIGN KEY (Offence_ID) REFERENCES Offence (Offence_ID),
  ADD CONSTRAINT fk_incident_people FOREIGN KEY (People_ID) REFERENCES People (People_ID),
  ADD CONSTRAINT fk_incident_ownership FOREIGN KEY (Ownership_ID) REFERENCES Ownership (Ownership_ID),
  ADD CONSTRAINT fk_incident_accounts FOREIGN KEY (Account_username) REFERENCES Accounts (Account_username);








DROP TABLE IF EXISTS Fines;
CREATE TABLE Fines (
  Fine_ID int(20) NOT NULL,
  Fine_amount int(20) NOT NULL,
  Fine_points int(20) NOT NULL,
  Incident_ID int(11) NOT NULL -- foreign key (Incident)
  -- There is a one-to-one relation ship between Incident and Fines. Because Fines is a individual object. 
    -- I put Incident_ID in Fines rather than the oppsite way, because it can reduce null value in Incident. 
    -- It also save space in the database.
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE Fines
  ADD PRIMARY KEY (Fine_ID);

INSERT INTO Fines (Fine_ID, Fine_amount, Fine_points, Incident_ID) VALUES
(1, 2000, 6, 3),
(2, 50, 0, 2),
(3, 500, 3, 4);

ALTER TABLE Fines
  MODIFY Fine_ID int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4,
  ADD CONSTRAINT fk_fines_incident FOREIGN KEY (Incident_ID) REFERENCES Incident (Incident_ID);









-- DROP TABLE IF EXISTS Fines;
-- CREATE TABLE Fines (
--   Fine_ID int(11) NOT NULL,
--   Fine_Amount int(11) NOT NULL,
--   Fine_Points int(11) NOT NULL,
--   Incident_ID int(11) NOT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- INSERT INTO Fines (Fine_ID, Fine_Amount, Fine_Points, Incident_ID) VALUES
-- (1, 2000, 6, 3),
-- (2, 50, 0, 2),
-- (3, 500, 3, 4);

-- DROP TABLE IF EXISTS Incident;
-- CREATE TABLE Incident (
--   Incident_ID int(11) NOT NULL,
--   Vehicle_ID int(11) DEFAULT NULL,
--   People_ID int(11) DEFAULT NULL,
--   Incident_Date date NOT NULL,
--   Incident_Report varchar(500) NOT NULL,
--   Offence_ID int(11) DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- INSERT INTO Incident (Incident_ID, Vehicle_ID, People_ID, Incident_Date, Incident_Report, Offence_ID) VALUES
-- (1, 15, 4, '2017-12-01', '40mph in a 30 limit', 1),
-- (2, 20, 8, '2017-11-01', 'Double parked', 4),
-- (3, 13, 4, '2017-09-17', '110mph on motorway', 1),
-- (4, 14, 2, '2017-08-22', 'Failure to stop at a red light - travelling 25mph', 8),
-- (5, 13, 4, '2017-10-17', 'Not wearing a seatbelt on the M1', 3);

-- DROP TABLE IF EXISTS Offence;
-- CREATE TABLE Offence (
--   Offence_ID int(11) NOT NULL,
--   Offence_description varchar(50) NOT NULL,
--   Offence_maxFine int(11) NOT NULL,
--   Offence_maxPoints int(11) NOT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- INSERT INTO Offence (Offence_ID, Offence_description, Offence_maxFine, Offence_maxPoints) VALUES
-- (1, 'Speeding', 1000, 3),
-- (2, 'Speeding on a motorway', 2500, 6),
-- (3, 'Seat belt offence', 500, 0),
-- (4, 'Illegal parking', 500, 0),
-- (5, 'Drink driving', 10000, 11),
-- (6, 'Driving without a licence', 10000, 0),
-- (7, 'Driving without a licence', 10000, 0),
-- (8, 'Traffic light offences', 1000, 3),
-- (9, 'Cycling on pavement', 500, 0),
-- (10, 'Failure to have control of vehicle', 1000, 3),
-- (11, 'Dangerous driving', 1000, 11),
-- (12, 'Careless driving', 5000, 6),
-- (13, 'Dangerous cycling', 2500, 0);

-- DROP TABLE IF EXISTS Ownership;
-- CREATE TABLE Ownership (
--   People_ID int(11) NOT NULL,
--   Vehicle_ID int(11) NOT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- INSERT INTO Ownership (People_ID, Vehicle_ID) VALUES
-- (3, 12),
-- (8, 20),
-- (4, 15),
-- (4, 13),
-- (1, 16),
-- (2, 14),
-- (5, 17),
-- (6, 18),
-- (7, 21);

-- DROP TABLE IF EXISTS People;
-- CREATE TABLE People (
--   People_ID int(11) NOT NULL,
--   People_name varchar(50) NOT NULL,
--   People_address varchar(50) DEFAULT NULL,
--   People_licence varchar(16) DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- INSERT INTO People (People_ID, People_name, People_address, People_licence) VALUES
-- (1, 'James Smith', '23 Barnsdale Road, Leicester', 'SMITH92LDOFJJ829'),
-- (2, 'Jennifer Allen', '46 Bramcote Drive, Nottingham', 'ALLEN88K23KLR9B3'),
-- (3, 'John Myers', '323 Derby Road, Nottingham', 'MYERS99JDW8REWL3'),
-- (4, 'James Smith', '26 Devonshire Avenue, Nottingham', 'SMITHR004JFS20TR'),
-- (5, 'Terry Brown', '7 Clarke Rd, Nottingham', 'BROWND3PJJ39DLFG'),
-- (6, 'Mary Adams', '38 Thurman St, Nottingham', 'ADAMSH9O3JRHH107'),
-- (7, 'Neil Becker', '6 Fairfax Close, Nottingham', 'BECKE88UPR840F9R'),
-- (8, 'Angela Smith', '30 Avenue Road, Grantham', 'SMITH222LE9FJ5DS'),
-- (9, 'Xene Medora', '22 House Drive, West Bridgford', 'MEDORH914ANBB223');

-- DROP TABLE IF EXISTS Vehicle;
-- CREATE TABLE Vehicle (
--   Vehicle_ID int(11) NOT NULL,
--   Vehicle_type varchar(20) NOT NULL,
--   Vehicle_colour varchar(20) NOT NULL,
--   Vehicle_licence varchar(7) DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- INSERT INTO Vehicle (Vehicle_ID, Vehicle_type, Vehicle_colour, Vehicle_licence) VALUES
-- (12, 'Ford Fiesta', 'Blue', 'LB15AJL'),
-- (13, 'Ferrari 458', 'Red', 'MY64PRE'),
-- (14, 'Vauxhall Astra', 'Silver', 'FD65WPQ'),
-- (15, 'Honda Civic', 'Green', 'FJ17AUG'),
-- (16, 'Toyota Prius', 'Silver', 'FP16KKE'),
-- (17, 'Ford Mondeo', 'Black', 'FP66KLM'),
-- (18, 'Ford Focus', 'White', 'DJ14SLE'),
-- (20, 'Nissan Pulsar', 'Red', 'NY64KWD'),
-- (21, 'Renault Scenic', 'Silver', 'BC16OEA'),
-- (22, 'Hyundai i30', 'Grey', 'AD223NG');


-- ALTER TABLE Fines
--   ADD PRIMARY KEY (Fine_ID),
--   ADD KEY Incident_ID (Incident_ID);

-- ALTER TABLE Incident
--   ADD PRIMARY KEY (Incident_ID),
--   ADD KEY fk_incident_vehicle (Vehicle_ID),
--   ADD KEY fk_incident_people (People_ID),
--   ADD KEY fk_incident_offence (Offence_ID);

-- ALTER TABLE Offence
--   ADD PRIMARY KEY (Offence_ID);

-- ALTER TABLE Ownership
--   ADD KEY fk_people (People_ID),
--   ADD KEY fk_vehicle (Vehicle_ID);

-- ALTER TABLE People
--   ADD PRIMARY KEY (People_ID);

-- ALTER TABLE Vehicle
--   ADD PRIMARY KEY (Vehicle_ID);


-- ALTER TABLE Fines
--   MODIFY Fine_ID int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
-- ALTER TABLE Incident
--   MODIFY Incident_ID int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
-- ALTER TABLE Offence
--   MODIFY Offence_ID int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
-- ALTER TABLE People
--   MODIFY People_ID int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
-- ALTER TABLE Vehicle
--   MODIFY Vehicle_ID int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

-- ALTER TABLE Fines
--   ADD CONSTRAINT fk_fines FOREIGN KEY (Incident_ID) REFERENCES Incident (Incident_ID);

-- ALTER TABLE Incident
--   ADD CONSTRAINT fk_incident_offence FOREIGN KEY (Offence_ID) REFERENCES Offence (Offence_ID),
--   ADD CONSTRAINT fk_incident_people FOREIGN KEY (People_ID) REFERENCES People (People_ID),
--   ADD CONSTRAINT fk_incident_vehicle FOREIGN KEY (Vehicle_ID) REFERENCES Vehicle (Vehicle_ID);

-- ALTER TABLE Ownership
--   ADD CONSTRAINT fk_person FOREIGN KEY (People_ID) REFERENCES People (People_ID),
--   ADD CONSTRAINT fk_vehicle FOREIGN KEY (Vehicle_ID) REFERENCES Vehicle (Vehicle_ID);

