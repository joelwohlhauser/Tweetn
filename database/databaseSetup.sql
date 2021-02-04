/* --------------- DATABASE --------------- */
CREATE DATABASE Tweetn;


/* --------------- USERS --------------- */
CREATE TABLE Tweetn.Users (
  `UserID` int NOT NULL AUTO_INCREMENT UNIQUE,
  `UserName` varchar(50) NOT NULL UNIQUE,
  `UserHash` varchar(1000) NOT NULL,
  `UserPrivateKey` TEXT NOT NULL,
  `UserPublicKey` TEXT NOT NULL,
  `UserAvatar` TEXT,
  `UserBio` varchar(200),
  `UserEmail` varchar(100),
  `UserBirthday` DATE,
  `UserJoined` DATE,
  PRIMARY KEY (`UserId`)
);


/* --------------- MESSAGES --------------- */
CREATE TABLE Tweetn.Messages (
  `MessageID` int NOT NULL AUTO_INCREMENT UNIQUE,
  `MessageSender` varchar(50) NOT NULL,
  `MessageReceiver` varchar(50) NOT NULL,
  `MessageContent` TEXT,
  `MessageTimestamp` TIMESTAMP,
  PRIMARY KEY (`MessageId`),
  CONSTRAINT FK_MessageSender FOREIGN KEY (MessageSender)
  REFERENCES Users(UserName),
  CONSTRAINT FK_MessageReceiver FOREIGN KEY (MessageReceiver)
  REFERENCES Users(UserName)
);


/* --------------- SENDEDMESSAGES --------------- */
CREATE TABLE Tweetn.SendedMessages (
  `SendedMessageID` int NOT NULL AUTO_INCREMENT UNIQUE,
  `SendedMessageUser` varchar(50) NOT NULL,
  `SendedMessageReceiver` varchar(50) NOT NULL,
  `SendedMessageContent` TEXT,
  `SendedMessageTimestamp` TIMESTAMP,
  PRIMARY KEY (`SendedMessageID`),
  CONSTRAINT FK_SendedMessageUser FOREIGN KEY (SendedMessageUser)
  REFERENCES Users(UserName),
  CONSTRAINT FK_SendedMessageReceiver FOREIGN KEY (SendedMessageReceiver)
  REFERENCES Users(UserName)
);


/* --------------- RELATIONSHIP --------------- */
CREATE TABLE Tweetn.Relationships (
  `RelationshipID` int NOT NULL AUTO_INCREMENT UNIQUE,
  `RelationshipUser` varchar(50) NOT NULL,
  `RelationshipFollowsUser` varchar(50) NOT NULL,
  PRIMARY KEY (`RelationshipID`),
  CONSTRAINT FK_RelationshipUser FOREIGN KEY (RelationshipUser)
  REFERENCES Users(UserName),
  CONSTRAINT FK_RelationshipFollowsUser FOREIGN KEY (RelationshipFollowsUser)
  REFERENCES Users(UserName)
);


/* --------------- POSTS --------------- */
CREATE TABLE Tweetn.Posts (
  `PostID` int NOT NULL AUTO_INCREMENT UNIQUE,
  `PostUser` varchar(50) NOT NULL,
  `PostContent` TEXT NOT NULL,
  `PostTimestamp` TIMESTAMP,
  PRIMARY KEY (`PostID`),
  CONSTRAINT FK_PostUser FOREIGN KEY (PostUser)
  REFERENCES Users(UserName)
);
