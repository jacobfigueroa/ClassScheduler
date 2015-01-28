SELECT DISTINCT Subject FROM TABLE1;

SELECT DISTINCT CourseNumber,Title FROM `TABLE1` WHERE Subject = "CSCI"

-- Find all ENG 1301 classes that are morning only on M W F
SELECT * FROM `TABLE1` WHERE Start > 900 AND Start < 1200 AND END < 1245 AND Subject = "ENG" AND CourseNumber = 1301 AND Days = "M W F"


