CREATE TRIGGER clear_comments AFTER delete ON files FOR EACH ROW DELETE from comments where file_id = OLD.files_id