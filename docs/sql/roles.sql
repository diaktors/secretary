# ************************************************************
# data needed for role table
# ************************************************************

INSERT INTO `role` (`id`, `parent_id`, `role_id`, `default`)
VALUES
	(1,NULL,'guest',1),
	(2,NULL,'user',0),
	(3,2,'keyuser',0),
	(4,3,'admin',0);
