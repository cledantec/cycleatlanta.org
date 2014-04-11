/*

re-make coord table w/ id column/key 

*/

ALTER TABLE coord DROP primary key,
ADD id INT PRIMARY KEY AUTO_INCREMENT FIRST,
ADD CONSTRAINT trip_coord UNIQUE (trip_id,recorded);


/*

merge coord_in to coord

*/

INSERT INTO coord (`trip_id`, `recorded`, `latitude`, `longitude`, `altitude`, `speed`, `hAccuracy`, `vAccuracy`) SELECT `trip_id`, `recorded`, `latitude`, `longitude`, `altitude`, `speed`, `hAccuracy`, `vAccuracy` FROM coord_in



/*

set trip stop time to last timestamp from coord

need to first turn off resetting start on update

*/


ALTER TABLE trip CHANGE `start` `start` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

UPDATE trip t1
JOIN ( SELECT trip_id, max(recorded) AS 'stop_real'
       FROM coord WHERE trip_id IN 
       (SELECT id FROM trip WHERE stop=0) 
       GROUP BY trip_id ) t2
ON t1.id = t2.trip_id
SET t1.stop = t2.stop_real;

ALTER TABLE trip CHANGE `start` `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

177-2013-10-17 14:29:37