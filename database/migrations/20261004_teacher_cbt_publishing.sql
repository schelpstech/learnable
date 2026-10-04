-- New drafts use direct teacher publishing. Existing assessment rows are unchanged.
ALTER TABLE cbt_assessments ALTER COLUMN require_approval SET DEFAULT 0;
