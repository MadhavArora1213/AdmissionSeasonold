-- Create scholarships table and seed data
USE edusearch;

CREATE TABLE IF NOT EXISTS `scholarships` (
  `id`                    INT AUTO_INCREMENT PRIMARY KEY,
  `name`                  VARCHAR(255) NOT NULL,
  `provider_name`         VARCHAR(255) NOT NULL,
  `category`              ENUM('GOVERNMENT','PRIVATE','INSTITUTIONAL','INTERNATIONAL') NOT NULL DEFAULT 'GOVERNMENT',
  `target_caste_category` VARCHAR(100) DEFAULT 'All Categories',
  `amount_inr`            INT DEFAULT NULL,
  `amount_description`    TEXT DEFAULT NULL,
  `deadline`              DATE DEFAULT NULL,
  `state_scope`           VARCHAR(100) DEFAULT NULL,
  `income_limit`          INT DEFAULT NULL,
  `application_link`      TEXT DEFAULT NULL,
  `status`                ENUM('ACTIVE','INACTIVE','EXPIRED') NOT NULL DEFAULT 'ACTIVE',
  `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Truncate and re-insert
TRUNCATE TABLE scholarships;

INSERT INTO scholarships (name, provider_name, category, target_caste_category, amount_inr, amount_description, deadline, state_scope, income_limit, application_link, status) VALUES
('NSP Central Sector Scheme', 'Ministry of Education, India', 'GOVERNMENT', 'EWS', 12000, NULL, '2026-10-31', NULL, 800000, 'https://scholarships.gov.in', 'ACTIVE'),
('Post Matric Scholarship for SC Students', 'Ministry of Social Justice', 'GOVERNMENT', 'SC', 15000, NULL, '2026-11-30', NULL, 250000, 'https://scholarships.gov.in', 'ACTIVE'),
('Post Matric Scholarship for ST Students', 'Ministry of Tribal Affairs', 'GOVERNMENT', 'ST', 15000, NULL, '2026-11-30', NULL, 250000, 'https://scholarships.gov.in', 'ACTIVE'),
('Prime Minister Scholarship Scheme', 'Kendriya Sainik Board', 'GOVERNMENT', 'All Categories', 25000, NULL, '2026-10-15', NULL, NULL, 'https://ksb.gov.in/pmss.htm', 'ACTIVE'),
('AICTE Pragati Scholarship for Girls', 'AICTE', 'GOVERNMENT', 'Girl Students', 50000, NULL, '2026-09-30', NULL, 800000, 'https://www.aicte-india.org', 'ACTIVE'),
('AICTE Saksham Scholarship', 'AICTE', 'GOVERNMENT', 'Differently Abled', 50000, NULL, '2026-09-30', NULL, 800000, 'https://www.aicte-india.org', 'ACTIVE'),
('INSPIRE Scholarship SHE', 'DST, Govt of India', 'GOVERNMENT', 'All Categories', 80000, NULL, '2026-11-30', NULL, NULL, 'https://online-inspire.gov.in', 'ACTIVE'),
('Ishan Uday NE Region Scholarship', 'UGC', 'GOVERNMENT', 'All Categories', 5400, NULL, '2026-10-31', NULL, 450000, 'https://scholarships.gov.in', 'ACTIVE'),
('OBC Pre-Matric Scholarship', 'Ministry of Social Justice', 'GOVERNMENT', 'OBC', 10000, NULL, '2026-10-31', NULL, 100000, 'https://scholarships.gov.in', 'ACTIVE'),
('Begum Hazrat Mahal Scholarship', 'Maulana Azad Foundation', 'GOVERNMENT', 'Minority', 12000, NULL, '2026-09-30', NULL, 200000, 'https://scholarships.gov.in', 'ACTIVE'),
('Rajasthan Merit Scholarship', 'Govt of Rajasthan', 'GOVERNMENT', 'All Categories', 10000, NULL, '2026-09-30', 'Rajasthan', 250000, 'https://sje.rajasthan.gov.in', 'ACTIVE'),
('Maharashtra Mahadbt Scholarship', 'Govt of Maharashtra', 'GOVERNMENT', 'OBC', 20000, NULL, '2026-10-15', 'Maharashtra', 300000, 'https://mahadbt.maharashtra.gov.in', 'ACTIVE'),
('UP Scholarship Post Matric', 'Govt of Uttar Pradesh', 'GOVERNMENT', 'SC', 12000, NULL, '2026-11-01', 'Uttar Pradesh', 250000, 'https://scholarship.up.gov.in', 'ACTIVE'),
('Kerala DCSE Scholarship', 'Govt of Kerala', 'GOVERNMENT', 'All Categories', 15000, NULL, '2026-09-15', 'Kerala', 600000, 'https://dcescholarship.kerala.gov.in', 'ACTIVE'),
('MP Pratibha Kiran Scholarship', 'Govt of Madhya Pradesh', 'GOVERNMENT', 'Girl Students', 5000, NULL, '2026-10-31', 'Madhya Pradesh', 150000, 'https://scholarshipportal.mp.nic.in', 'ACTIVE'),
('Bihar SC/ST Scholarship', 'Govt of Bihar', 'GOVERNMENT', 'SC', 8000, NULL, '2026-10-01', 'Bihar', 250000, 'https://pmsonline.bih.nic.in', 'ACTIVE'),
('Punjab Scholarship for Students', 'Govt of Punjab', 'GOVERNMENT', 'OBC', 10000, NULL, '2026-09-30', 'Punjab', 300000, 'https://scholarships.punjab.gov.in', 'ACTIVE'),
('Tata Scholarship Cornell University', 'Tata Trusts', 'PRIVATE', 'All Categories', NULL, 'Full tuition + living expenses', '2026-12-01', NULL, NULL, 'https://www.tatatrusts.org', 'ACTIVE'),
('Reliance Foundation UG Scholarship', 'Reliance Foundation', 'PRIVATE', 'All Categories', 200000, NULL, '2026-11-15', NULL, 250000, 'https://rf.foundation', 'ACTIVE'),
('Aga Khan Foundation Scholarship', 'Aga Khan Foundation', 'PRIVATE', 'Minority', NULL, '50% grant + 50% soft loan', '2026-03-31', NULL, NULL, 'https://www.akdn.org', 'ACTIVE'),
('Infosys Foundation Scholarship', 'Infosys Foundation', 'PRIVATE', 'SC', 100000, NULL, '2026-10-31', NULL, 300000, 'https://www.infosys.org', 'ACTIVE'),
('Wipro Cares Scholarship', 'Wipro Foundation', 'PRIVATE', 'All Categories', 30000, NULL, '2026-09-30', NULL, 200000, 'https://www.wipro.com', 'ACTIVE'),
('JN Tata Endowment Loan Scholarship', 'JN Tata Endowment', 'INSTITUTIONAL', 'All Categories', NULL, 'Loan scholarship for higher studies abroad', '2026-03-15', NULL, NULL, 'https://www.jntataendowment.org', 'ACTIVE'),
('BITS Pilani Merit Scholarship', 'BITS Pilani', 'INSTITUTIONAL', 'All Categories', 50000, 'Fee waiver up to 50%', '2026-08-01', NULL, NULL, 'https://www.bits-pilani.ac.in', 'ACTIVE'),
('National Sports Talent Scholarship', 'SAI, Govt of India', 'GOVERNMENT', 'Sports', 60000, NULL, '2026-10-01', NULL, NULL, 'https://sportsauthorityofindia.nic.in', 'ACTIVE');
