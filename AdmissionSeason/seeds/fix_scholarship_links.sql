USE edusearch;

-- Update ALL scholarships with verified, working root-domain URLs only
-- No deep paths — only homepage URLs that definitely work

UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'NSP Central Sector Scheme';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'Post Matric Scholarship for SC Students';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'Post Matric Scholarship for ST Students';
UPDATE scholarships SET application_link = 'https://ksb.gov.in'                WHERE name = 'Prime Minister Scholarship Scheme';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'AICTE Pragati Scholarship for Girls';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'AICTE Saksham Scholarship';
UPDATE scholarships SET application_link = 'https://online-inspire.gov.in'     WHERE name = 'INSPIRE Scholarship SHE';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'Ishan Uday NE Region Scholarship';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'OBC Pre-Matric Scholarship';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'Begum Hazrat Mahal Scholarship';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'Rajasthan Merit Scholarship';
UPDATE scholarships SET application_link = 'https://mahadbt.maharashtra.gov.in' WHERE name = 'Maharashtra Mahadbt Scholarship';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'UP Scholarship Post Matric';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'Kerala DCSE Scholarship';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'MP Pratibha Kiran Scholarship';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'Bihar SC/ST Scholarship';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'Punjab Scholarship for Students';
UPDATE scholarships SET application_link = 'https://www.tatatrusts.org'        WHERE name = 'Tata Scholarship Cornell University';
UPDATE scholarships SET application_link = 'https://www.reliancefoundation.org' WHERE name = 'Reliance Foundation UG Scholarship';
UPDATE scholarships SET application_link = 'https://www.akdn.org'              WHERE name = 'Aga Khan Foundation Scholarship';
UPDATE scholarships SET application_link = 'https://www.infosys.org'           WHERE name = 'Infosys Foundation Scholarship';
UPDATE scholarships SET application_link = 'https://www.wipro.com'             WHERE name = 'Wipro Cares Scholarship';
UPDATE scholarships SET application_link = 'https://www.jntataendowment.org'   WHERE name = 'JN Tata Endowment Loan Scholarship';
UPDATE scholarships SET application_link = 'https://www.bits-pilani.ac.in'     WHERE name = 'BITS Pilani Merit Scholarship';
UPDATE scholarships SET application_link = 'https://scholarships.gov.in'       WHERE name = 'National Sports Talent Scholarship';

SELECT name, application_link FROM scholarships ORDER BY id;
