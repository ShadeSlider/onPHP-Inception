


CREATE TABLE "backend_user" (
    
);


CREATE SEQUENCE "backend_user_id_seq";
ALTER TABLE "backend_user" ADD COLUMN "id" INTEGER NOT NULL default nextval('backend_user_id_seq');
ALTER TABLE "backend_user" ADD PRIMARY KEY("id");
ALTER TABLE "backend_user" ADD COLUMN "created_at" TIMESTAMP WITH TIME ZONE NULL;
ALTER TABLE "backend_user" ADD COLUMN "updated_at" TIMESTAMP WITH TIME ZONE NULL;
ALTER TABLE "backend_user" ADD COLUMN "deleted_at" TIMESTAMP WITH TIME ZONE NULL;
ALTER TABLE "backend_user" ADD COLUMN "display_order" INTEGER NULL DEFAULT '0';
ALTER TABLE "backend_user" ADD COLUMN "is_active" BOOLEAN NULL DEFAULT TRUE;
ALTER TABLE "backend_user" ADD COLUMN "is_visible" BOOLEAN NULL DEFAULT TRUE;
ALTER TABLE "backend_user" ADD COLUMN "is_deleted" BOOLEAN NULL DEFAULT FALSE;
ALTER TABLE "backend_user" ADD COLUMN "email" CHARACTER VARYING(255) NOT NULL;
ALTER TABLE "backend_user" ADD COLUMN "login" CHARACTER VARYING(255) NOT NULL;
ALTER TABLE "backend_user" ADD COLUMN "password" CHARACTER VARYING(32) NOT NULL;
ALTER TABLE "backend_user" ADD COLUMN "last_name" CHARACTER VARYING(255) NULL;
ALTER TABLE "backend_user" ADD COLUMN "first_name" CHARACTER VARYING(255) NULL;
ALTER TABLE "backend_user" ADD COLUMN "mid_name" CHARACTER VARYING(255) NULL;
ALTER TABLE "backend_user" ADD COLUMN "mobile_phone" CHARACTER VARYING(255) NULL;
ALTER TABLE "backend_user" ADD COLUMN "work_phone" CHARACTER VARYING(255) NULL;
ALTER TABLE "backend_user" ADD COLUMN "gender" CHARACTER VARYING(1) NULL DEFAULT 'u';
ALTER TABLE "backend_user" ADD COLUMN "text_short" CHARACTER VARYING(65536) NULL;
ALTER TABLE "backend_user" ADD COLUMN "text" CHARACTER VARYING(65536) NULL;
ALTER TABLE "backend_user" ADD COLUMN "is_super_admin" BOOLEAN NULL DEFAULT FALSE;
ALTER SEQUENCE "backend_user_id_seq" OWNED BY "backend_user"."id";

CREATE TABLE "backend_user_role" (
    
);


CREATE SEQUENCE "backend_user_role_id_seq";
ALTER TABLE "backend_user_role" ADD COLUMN "id" INTEGER NOT NULL default nextval('backend_user_role_id_seq');
ALTER TABLE "backend_user_role" ADD PRIMARY KEY("id");
ALTER TABLE "backend_user_role" ADD COLUMN "created_at" TIMESTAMP WITH TIME ZONE NULL;
ALTER TABLE "backend_user_role" ADD COLUMN "updated_at" TIMESTAMP WITH TIME ZONE NULL;
ALTER TABLE "backend_user_role" ADD COLUMN "deleted_at" TIMESTAMP WITH TIME ZONE NULL;
ALTER TABLE "backend_user_role" ADD COLUMN "display_order" INTEGER NULL DEFAULT '0';
ALTER TABLE "backend_user_role" ADD COLUMN "is_active" BOOLEAN NULL DEFAULT TRUE;
ALTER TABLE "backend_user_role" ADD COLUMN "is_visible" BOOLEAN NULL DEFAULT TRUE;
ALTER TABLE "backend_user_role" ADD COLUMN "is_deleted" BOOLEAN NULL DEFAULT FALSE;
ALTER TABLE "backend_user_role" ADD COLUMN "parent_id" INTEGER NULL REFERENCES "backend_user_role"("id") ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE "backend_user_role" ADD COLUMN "name" CHARACTER VARYING(255) NOT NULL;
ALTER TABLE "backend_user_role" ADD COLUMN "title" CHARACTER VARYING(255) NOT NULL DEFAULT '';
ALTER SEQUENCE "backend_user_role_id_seq" OWNED BY "backend_user_role"."id";

CREATE INDEX "parent_id_idx__backend_user_role" ON "backend_user_role"("parent_id");

CREATE TABLE "backend_access_resource" (
    
);


CREATE SEQUENCE "backend_access_resource_id_seq";
ALTER TABLE "backend_access_resource" ADD COLUMN "id" INTEGER NOT NULL default nextval('backend_access_resource_id_seq');
ALTER TABLE "backend_access_resource" ADD PRIMARY KEY("id");
ALTER TABLE "backend_access_resource" ADD COLUMN "created_at" TIMESTAMP WITH TIME ZONE NULL;
ALTER TABLE "backend_access_resource" ADD COLUMN "updated_at" TIMESTAMP WITH TIME ZONE NULL;
ALTER TABLE "backend_access_resource" ADD COLUMN "deleted_at" TIMESTAMP WITH TIME ZONE NULL;
ALTER TABLE "backend_access_resource" ADD COLUMN "display_order" INTEGER NULL DEFAULT '0';
ALTER TABLE "backend_access_resource" ADD COLUMN "is_active" BOOLEAN NULL DEFAULT TRUE;
ALTER TABLE "backend_access_resource" ADD COLUMN "is_visible" BOOLEAN NULL DEFAULT TRUE;
ALTER TABLE "backend_access_resource" ADD COLUMN "is_deleted" BOOLEAN NULL DEFAULT FALSE;
ALTER TABLE "backend_access_resource" ADD COLUMN "parent_id" INTEGER NULL REFERENCES "backend_access_resource"("id") ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE "backend_access_resource" ADD COLUMN "type_id" BIGINT NOT NULL;
ALTER TABLE "backend_access_resource" ADD COLUMN "name" CHARACTER VARYING(255) NOT NULL;
ALTER TABLE "backend_access_resource" ADD COLUMN "title" CHARACTER VARYING(255) NOT NULL;
ALTER SEQUENCE "backend_access_resource_id_seq" OWNED BY "backend_access_resource"."id";

CREATE INDEX "parent_id_idx__backend_access_resource" ON "backend_access_resource"("parent_id");

CREATE INDEX "type_id_idx__backend_access_resource" ON "backend_access_resource"("type_id");

CREATE TABLE "employee" (
    
);


CREATE SEQUENCE "employee_id_seq";
ALTER TABLE "employee" ADD COLUMN "id" INTEGER NOT NULL default nextval('employee_id_seq');
ALTER TABLE "employee" ADD PRIMARY KEY("id");
ALTER TABLE "employee" ADD COLUMN "email" CHARACTER VARYING(255) NULL;
ALTER TABLE "employee" ADD COLUMN "login" CHARACTER VARYING(255) NULL;
ALTER TABLE "employee" ADD COLUMN "first_name" CHARACTER VARYING(255) NOT NULL;
ALTER TABLE "employee" ADD COLUMN "last_name" CHARACTER VARYING(255) NOT NULL;
ALTER TABLE "employee" ADD COLUMN "middle_name" CHARACTER VARYING(255) NULL;
ALTER TABLE "employee" ADD COLUMN "mobile_phone" CHARACTER VARYING(255) NULL;
ALTER TABLE "employee" ADD COLUMN "work_phone" CHARACTER VARYING(255) NULL;
ALTER TABLE "employee" ADD COLUMN "gender" CHARACTER VARYING(1) NULL DEFAULT 'u';
ALTER TABLE "employee" ADD COLUMN "position" CHARACTER VARYING(255) NULL;
ALTER TABLE "employee" ADD COLUMN "birth_date" DATE NULL;
ALTER SEQUENCE "employee_id_seq" OWNED BY "employee"."id";

CREATE INDEX "full_name_idx__employee" ON "employee"("first_name", "last_name", "middle_name");

CREATE INDEX "mobile_phone_idx__employee" ON "employee"("mobile_phone");

CREATE INDEX "birth_date_idx__employee" ON "employee"("birth_date");

CREATE UNIQUE INDEX "email_uidx__employee" ON "employee"("email");

CREATE UNIQUE INDEX "login_uidx__employee" ON "employee"("login");

CREATE TABLE "backend_user__backend_user_role" (
    "backend_user_role_id" INTEGER NOT NULL REFERENCES "backend_user_role"("id") ON DELETE CASCADE ON UPDATE CASCADE,
    "backend_user_id" INTEGER NOT NULL REFERENCES "backend_user"("id") ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE("backend_user_role_id", "backend_user_id")
);


CREATE TABLE "backend_user_role__backend_access_resource" (
    "backend_access_resource_id" INTEGER NOT NULL REFERENCES "backend_access_resource"("id") ON DELETE CASCADE ON UPDATE CASCADE,
    "backend_user_role_id" INTEGER NOT NULL REFERENCES "backend_user_role"("id") ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE("backend_access_resource_id", "backend_user_role_id")
);




