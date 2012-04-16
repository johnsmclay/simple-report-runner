Select 
    CONCAT(ut.first_name,' ',ut.last_name) as 'Teacher',
    sch.school_name as 'School',
    concat(sec.section_name,if(sec.client_section_id is null 
        or trim(sec.client_section_id)='','',concat(' (',sec.client_section_id,')'))) as 'Classroom',
    c.title as 'Course',
    sec.lessons as 'Class Days',
    u.first_name as 'First Name',
    u.last_name as 'Last Name',
    Coalesce(if(u.client_student_id is null or trim(u.client_student_id)
                ='',null,u.client_student_id),u.username) as 'Student ID',
    if((trim(u.email) REGEXP '^[A-Z0-9._%+-]+[[.commercial-at.]]{1}[A-Z0-9.-]+[[.period.]]{1}[A-Z]{2,6}$'
        OR (trim(u.email) NOT REGEXP '^[[.commercial-at.][.period.]]|[[.commercial-at.][.period.]]$' 
        and length(trim(u.email))>2)),trim(u.email),'') as 'Student E-mail',
    if(u.last_login > enrollments.added,DATE_FORMAT(u.last_login,'%m/%d/%Y'),'') as 'Last Login',
    DATE_FORMAT(enrollments.added,'%m/%d/%Y') as 'Enrollment Date',
    concat(
            if(coalesce(enrollments.expires,sec.expires,1)=1,'(est) ',''),
            DATE_FORMAT(
        coalesce(enrollments.expires,sec.expires,convert(sec.end_date,datetime), DATE_ADD(enrollments.added,INTERVAL floor(if(sch.id in (736,529,170,1258,1259,119,707,461,1203,487,488,778,783,779,784,1256,1257,428),365,sec.lessons * 1.5)) DAY))
            ,'%m/%d/%Y')
            ) as 'Enrollment Locking Date',
    ROUND((wse.points_earned/(wse.points_possible_complete + wse.points_past_due))*100, 0) as'Current Grade Percentage',
    CONCAT(wse.points_earned,'/',(wse.points_possible_complete + wse.points_past_due)) as 'Current Grade Points',
    ROUND((wse.points_earned/wse.points_possible_total)*100, 0) as'Final Grade Percentage',
    CONCAT(wse.points_earned,'/',wse.points_possible_total)  as 'Final Grade Points',
    ROUND((wse.assignments_complete/wse.assignments_total)*100,0) as 'Percentage of Activities Completed',
    CONCAT(wse.assignments_complete,'/',wse.assignments_total) as 'Activities Completed/Course Total',
    CASE
        WHEN wse.exam_completion_percentage = 100 THEN 'yes'
        ELSE 'no'
    END AS 'Took Finals'
FROM pglms2010.sections_users enrollments
left join pglms2010.sections sec on sec.id = enrollments.section_id
left join pglms2010.schools sch on sch.id = sec.school_id
left join pglms2010.courses c on c.id  = sec.course_id
left join pglms2010.users u on u.id = enrollments.user_id
left join warehouse.student_enrollments wse on wse.section_user_id = enrollments.id
left join pglms2010.users ut on ut.id = sec.teacher
    WHERE (sec.start_date <= NOW() OR sec.start_date IS NULL)
    and enrollments.role = 'Student'
    and (enrollments.dropped IS NULL OR enrollments.dropped = 0)
    and enrollments.added < now()
    AND sec.teacher IN (SELECT DISTINCT system_teacher_id FROM warehouse.mil_pglms_teacher_map WHERE system = 'PGLMS')
    AND coalesce(enrollments.expires,IFNULL(sec.expires,'9999-12-31 00:00:00')) > NOW()
    AND (if(u.last_login > enrollments.added,u.last_login,'9999-12-31 00:00:00') > DATE_SUB(NOW(),INTERVAL 1 YEAR))
Group by enrollments.id
ORDER BY CONCAT(ut.first_name,' ',ut.last_name),sch.school_name,sec.section_name,u.first_name,u.last_name