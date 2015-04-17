<?php $html->css('links_page', null, array(), false); ?>
<?php $this->set('subnavcontent', $this->element('news_posts_subnav')); ?>
<?php $javascript->link(array('links_page'), false); ?>
<?php $this->pageTitle = 'Baystate Roads &rsaquo; Links'; ?>
<?php $this->set('bodyClass', 'links-page'); ?>

<h2>Links</h2>

<?php echo $link->links_categories(); ?>

<div class="link-section" id="bsr-links">
	<h3 class="section-header">Baystate Roads' Links of Interest</h3>
	<div class="go-to-top"><a href="#">Top &uarr;</a></div>
	
	<?php
		echo $link->links_make_link(
			'National LTAP Association',
			"This is the official site for the Local Technical Assistance Program (LTAP) or Technology Transfer (T2) organization.",
			'http://www.ltap.org/',
			'links/ltap.jpg',
			'LTAP Centers Thumbnail'
		);
		
		echo $link->links_make_link(
			'Intelligent Transportation Society of America (ITSA)',
			"The goal of ITS is to increase the safety and efficiency of surface transportation using advance technologies.",
			'http://www.itsa.org/',
			'links/itsa.jpg',
			'ITSA Thumbnail'
		);
		
		echo $link->links_make_link(
			'Consortium for ITS Training and Education (CITE)',
			"Web-based ITS Training source that is flexible, just-in-time, and interactive. CEUs are available in courses ranging from traffic signal timing to Evaluating ITS Projects.",
			'http://citeconsortium.org/',
			'links/cite.jpg',
			'CITE Thumbnail'
		);
		
		echo $link->links_make_link(
			'American Association of State Highway and Transportation Officials (AASHTO)',
			"AASHTO is a nonprofit, nonpartisan association representing highway and transportation departments throughout the United States and Puerto Rico. AASHTO's aim is to foster the development, operation and maintenance of an integrated national transportation system.",
			'http://www.transportation.org/',
			'links/aashto.jpg',
			'AASHTO Thumbnail'
		);
		
		echo $link->links_make_link(
			'American Society of Civil Engineers (ASCE)',
			"The ASCE website allows you to search their database for people and projects. The public can research and understand the history of civil engineering.",
			'http://www.asce.org/',
			'links/asce.jpg',
			'ASCE Thumbnail'
		);
		
		echo $link->links_make_link(
			'Asphalt Institute',
			"The Asphalt Institute is a U.S. based association of international petroleum asphalt producers, manufacturers, and affiliated businesses. Its mission is to promote the use, benefits and quality performance of petroleum asphalt.",
			'http://www.asphaltinstitute.org/',
			'links/ai.jpg',
			'AI Thumbnail'
		);
		
		echo $link->links_make_link(
			'United States Department of Transportation (USDOT)',
			"You can find out information about the Department's organization, key officials, mission and other important information.",
			'http://www.dot.gov/',
			'links/dot.jpg',
			'USDOT Thumbnail'
		);
		
		echo $link->links_make_link(
			'American Public Works Association (APWA)',
			"The American Public Works Association is an international educational and professional association of public agencies, private sector companies, and individuals dedicated to providing high quality public works goods and services. See also the " . $html->link('New England chapter', 'http://newengland.apwa.net/') . '.',
			'http://www.apwa.net/',
			'links/apwa.jpg',
			'APWA Thumbnail'
		);
		
		echo $link->links_make_link(
			'National Transportation Library (NTL)',
			"NTL improves the availability of transportation-related information needed by Federal, state and local decision-makers. NTL's mission is to decrease timely access to the information that supports transportation policy, research, operations and technology transfer activities.",
			'http://ntl.bts.gov/',
			'links/ntl.jpg',
			'NTL Thumbnail'
		);
		
		echo $link->links_make_link(
			'Official Massachusetts Website',
			"You will be able to find information about the Commonwealth's elected officials, laws, history, symbols, tourist destinations, etc.",
			'http://mass.gov/',
			'links/mass.jpg',
			'Mass.gov Thumbnail'
		);

		echo $link->links_make_link(
			'Massachusetts Department of Public Utilities',
			"Information pertinent to consumer protection in trasportation, utilities, and energy.",
			'http://www.mass.gov/eea/grants-and-tech-assistance/guidance-technical-assistance/agencies-and-divisions/dpu/',
			'links/massdpu.jpg',
			'Massachusetts DPU Thumbnail'
		);
		
		echo $link->links_make_link(
			'Massachusetts Department of Transportation (MassDOT)',
			"MassDOT's mission is to deliver safe and efficent transportation services across the Commonwealth by building a culture of innovation and respect that makes customer service and public safety top prioities. MassDOT includes four Divisions: Highway, Transit, Aeronautics, and Registry of Motor Vehicles.",
			'http://www.massdot.state.ma.us/',
			'links/massdot.jpg',
			'MassDOT Thumbnail'
		);
		
		echo $link->links_make_link(
			'Massachusetts - Operational Services Division (OSD)',
			"The Operational Services Division (OSD) facilitates and audits the acquisition of commodities, professional, general, human and social services that support the socio-economic goals of the Commonwealth including disadvantaged business, environmental, and other programs that are in the best interest of the Commonwealth. Additionally, OSD has the responsibility for the administration of printing, fleet operations and surplus personal property disposition.",
			'http://www.mass.gov/anf/budget-taxes-and-procurement/oversight-agencies/osd/',
			'links/massosd.jpg',
			'OSD Thumbnail'
		);
		
		echo $link->links_make_link(
			'MassDOT Highway Division',
			"The Highway Division includes the roadways, bridges, and tunnels of the former Massachusetts Highway Department and Massachusetts Turnpike Authority. The Tobin Bridge joins the Division, effective January 1, 2010. The Division also includes many bridges and parkways previously under the authority of the Department of Conservation and Recreation. The Highway Division is responsible for the design, construction and maintenance of the Commonwealth's state highways and bridges. The Division is responsible for overseeing traffic safety and engineering activities including the Highway Operations Control Center to ensure safe road and travel conditions.",
			"http://www.massdot.state.ma.us/Highway/",
			'links/massdothw.jpg',
			'MassDOT Highway Thumbnail'
		);
		
		echo $link->links_make_link(
			'The Massachusetts Highway Association (MHA)',
			"The Massachusetts Highway Association (MHA) is an umbrella organization which encompasses seven county highway associations: Barnstable, Plymouth, Norfolk/Bristol/Middlesex, Worcester, Berkshire, Essex and Tri-County. Founded in 1893 with a membership of 51, the Massachusetts Highway Association today boasts a membership of more than 750. MHA membership comprises of highway officials, including directors, engineers, and superintendents involved in all phases of highway related activities.",
			'http://www.masshwy.org/',
			'links/mha.jpg',
			'MHA Thumbnail'
		);
		
		echo $link->links_make_link(
			'Massachusetts Bay Transportation Authority (MBTA)',
			"This is the nation's oldest and fourth largest transportation system. You can find schedules, maps, fares, or even purchase a pass online.",
			'http://mbta.com/',
			'links/mbta.jpg',
			'MBTA Thumbnail'
		);
		
		echo $link->links_make_link(
			'Central Artery Program',
			'At this website you can view images of progress on the "Big Dig," look at project maps, current expenses, planning proposals, equipment or different routes to take during rush hours.',
			'http://www.cecilgroup.com/urban-design/central-arterytunnel-leverett-circle-surface-restoration/',
			'links/centralartery.jpg',
			'Central Artery Thumbnail'
		);
		
		echo $link->links_make_link(
			"Commonwealth of Massachusetts' Procurement Access & Solicitation System (COMM-PASS)",
			'A site for procurement and solicitation in the Commonwealth.',
			'http://www.comm-pass.com/',
			'links/comm-pass.jpg',
			'COMM-PASS Thumbnail'
		);
		
		echo $link->links_make_link(
			'Massachusetts Department of Conservation and Recreation (DCR)',
			'The DCR was formed by the merger of the Department of Environmental Management and the Metropolitan District Commission in July 2003. We are in the process of integrating our websites, but you can find the information you need by clicking on the link above.',
			'http://www.mass.gov/dcr/',
			'links/dcr.jpg',
			'DCR Thumbnail'
		);
		
		echo $link->links_make_link(
			'Massachusetts Department of Environmental Protection (MassDEP)',
			'This state agency is responsible for protecting human health and the environment by ensuring clean air and water, the safe management and disposal of solid and hazardous wastes, the timely cleanup of hazardous waste sites and spills, and the preservation of wetlands and coastal resources.',
			'http://www.mass.gov/dep/',
			'links/mass-dep.jpg',
			'MassDEP Thumbnail'
		);
		
		echo $link->links_make_link(
			'Road Roughness Home',
			'This is the home page for accessing information, documentation, software, and data associated with road roughness.',
			'http://www.umtri.umich.edu/divisionPage.php?pageID=62',
			'links/roughness.jpg',
			'Road Roughness Thumbnail'
		);
		
		echo $link->links_make_link(
			'Superpave Center',
			"Strategic Highway Research Program (SHRP) conducted a $50 million research effort to develop new ways to specify, test, and design asphalt materials. The final product of the SHRP asphalt research program is a new system referred to as Superpave, which stands for Superior Performing Asphalt Pavements. It represents an improved system for specifying the components of asphalt concrete, asphalt mixture design and analysis, and asphalt pavement performance prediction.",
			'http://www.utexas.edu/research/superpave/index.html',
			'links/superpave.jpg',
			'Superpave Center Thumbnail'
		);
	?>
	
	<?php echo $link->links_categories(); ?>
</div>



<div id="web-training" class="link-section">
	<h3 class="section-header">Web Based Training</h3>
	<div class="go-to-top"><a href="#">Top &uarr;</a></div>
	
	<?php
		echo $link->links_make_link(
			'Web-Based Training, ITS & Traffic Courses, Costs Associated',
			'',
			'http://www.citeconsortium.org/curriculum.html',
			'links/cite.jpg',
			'CITE Thumbnail'
		);
		
		echo $link->links_make_link(
			'Web-Based Training - Hazardous Materials, Costs Associated',
			'',
			'http://envirospectrum.com/course-cat.htm',
			'links/envirospectrum.jpg',
			'EnviroSpectrum Thumbnail'
		);
		
		echo $link->links_make_link(
			'GIS on-line training tools',
			'',
			'http://training.esri.com/gateway/index.cfm?fa=search.results&cannedsearch=2',
			'links/esri.jpg',
			'ESRI Thumbnail'
		);
		
		echo $link->links_make_link(
			'GIS on-line training tools',
			'',
			'http://www.nps.gov/gis/outreach/training.html',
			'links/national-parks.jpg',
			'National Park Services Thumbnail'
		);
		
		echo $link->links_make_link(
			'EPA Self-Instructional Courses',
			'',
			'http://www.epa.gov/apti/catalog/catsic.html',
			'links/apti.jpg',
			'APTI Thumbnail'
		);
		
		echo $link->links_make_link(
			'Click, Listen & Learn, APWA, Costs Associated',
			'',
			'http://www.apwa.net/Education/',
			'links/apwa.jpg',
			'APWA Thumbnail'
		);
		
		echo $link->links_make_link(
			'Click, Listen & Learn, APWA, Costs Associated',
			'',
			'http://www.apwa.net/Education/CLL/',
			'links/apwa.jpg',
			'APWA Thumbnail'
		);
		
		echo $link->links_make_link(
			'Modified Asphalt Course, Video On Demand, CTDOT',
			'',
			'http://www.ct.gov/dot/cwp/view.asp?a=1617&Q=281128&PM=1',
			'links/ctdot.jpg',
			'CTDOT Thumbnail'
		);
		
		echo $link->links_make_link(
			'Center for Transportation and the Environment, CTE Technology Transfer Video on Demand',
			'',
			'http://itre.ncsu.edu/cte/techtransfer/teleconferences/archive.asp',
			'links/cte.jpg',
			'CTE Thumbnail'
		);
		
		echo $link->links_make_link(
			'Paauwerfully Organized, Teleclasses - Office Organization, Costs Associated',
			'',
			'http://www.orgcoach.net/teleclasses.html#buriedinpaper',
			'links/paau.jpg',
			'Paauwerfully Organized Thumbnail'
		);
		
		echo $link->links_make_link(
			'Find Anything in 5 Seconds or Less Audio Course, Paauwerfully Organized',
			'',
			'http://thepapertiger.net/audioblogs/Find_Anything_Teleclass.mp3',
			'links/paau.jpg',
			'Paauwerfully Organized Thumbnail'
		);
		
		echo $link->links_make_link(
			"ASCE Distance Learning - Web Seminars, CD's Available, Cost Associated",
			'',
			'http://www.asce.org/ProgramProductLine.aspx?id=87',
			'links/asce.jpg',
			'ASCE Thumbnail'
		);
		
		echo $link->links_make_link(
			"Institute of Transportation Engineers, Online Learning Gateway, Cost Associated Transportation Planning, Traffic Control Devices, Capacity/Safety Analysis",
			'',
			'http://www.ite.org/education/olg.asp',
			'links/ite.jpg',
			'ITE Thumbnail'
		);
		
		echo $link->links_make_link(
			'FMCSA Federal Information Systems Security Awareness Online Course',
			'',
			'http://www.fmcsa.dot.gov/ntc/security/content/index2.htm',
			'links/dot.jpg',
			'USDOT Thumbnail'
		);
		
		echo $link->links_make_link(
			'Introduction to Commercial Vehicle Operations CVO-CVISN 101 - Cost Associated',
			'',
			'http://www.citeconsortium.org/courses/2mod3.html',
			'links/cite.jpg',
			'CITE Thumbnail'
		);
		
		echo $link->links_make_link(
			'Institute of Transportation Studies, 421 Short Courses, Some Cost Associated',
			'',
			'http://www.techtransfer.berkeley.edu/clearinghouse/index.php',
			'links/berkeley.jpg',
			'Berkeley Thumbnail'
		);
		
		echo $link->links_make_link(
			'Online Hazmat School - Cost Associated',
			'',
			'http://www.hazmatschool.com/HScourses.html',
			'links/hazmat.jpg',
			'Hazmat School Thumbnail'
		);
		
		echo $link->links_make_link(
			'AGC Online Institute - Cost Associated',
			'',
			'http://agc.advanceonline.com/index.htm',
			'links/agc.jpg',
			'AGC Thumbnail'
		);
		
		echo $link->links_make_link(
			'GIS and Mapping Software Online Training',
			'',
			'http://training.esri.com/gateway/index.cfm',
			'links/esri.jpg',
			'ESRI Thumbnail'
		);
		
		echo $link->links_make_link(
			'College of Extended Studies, Online Courses - Cost Associated',
			'',
			'http://www.ces.sdsu.edu/webcourses.html',
			'links/sdsu.jpg',
			'SDSU Thumbnail'
		);
		
		echo $link->links_make_link(
			'Red Vector Online Courses - Cost Associated',
			'',
			'http://www.redvector.com/',
			'links/red-vector.jpg',
			'Red Vector Thumbnail'
		);
		
		echo $link->links_make_link(
			'NTOC - Talking Operations Web Conferences',
			'',
			'http://www.ntoctalks.com/web_casts.php',
			'links/ntoc.jpg',
			'NTOC Thumbnail'
		);
		
		echo $link->links_make_link(
			'NTOC - Talking Operations Web Conferences Archives',
			'',
			'http://www.ntoctalks.com/web_casts_archive.php',
			'links/ntoc.jpg',
			'NTOC Thumbnail'
		);
		
		echo $link->links_make_link(
			'NTOC - Talking Operations Training Calendar',
			'',
			'http://ntoctalks.com/training_calendar/index.php',
			'links/ntoc.jpg',
			'NTOC Thumbnail'
		);
		
		echo $link->links_make_link(
			'Unstrung Wireless Communications, IT Webinars, Archived Webinars',
			'',
			'http://www.lightreading.com/webinars.asp',
			'links/light-reading.jpg',
			'Light Reading Thumbnail'
		);
		
		echo $link->links_make_link(
			'Development of E-Learning Resources',
			'',
			'http://www.bersin.com/',
			'links/bersin.jpg',
			'Bersin & Associates Thumbnail'
		);
		
		echo $link->links_make_link(
			'Traffic Noise Training On-Line',
			'',
			'http://www.dot.ca.gov/hq/env/noise/training.htm',
			'links/ca-dot.jpg',
			'California DOT Thumbnail'
		);
		
		echo $link->links_make_link(
			'Online Pavement Design Training, TAMU',
			'',
			'http://pavementdesign.tamu.edu/',
			'links/tx-dot.jpg',
			'Texas DOT Thumbnail'
		);
		
		echo $link->links_make_link(
			'Maine DEP Online Training Courses',
			'',
			'http://www.maine.gov/dep/gis/training/',
			'links/me-dep.jpg',
			'Maine DEP Thumbnail'
		);
		
		echo $link->links_make_link(
			'Leadership & Business Web Seminar Achieves',
			'',
			'http://main.placeware.com/demos/web_seminar_archive.cfm',
			'links/blank.jpg',
			'Thumbnail'
		);
		
		echo $link->links_make_link(
			'Indirect and Cummulative Impacts, Webcast Archieve',
			'',
			'http://dotmedia.wi.gov/main/viewer/?peid=04771335-493d-4a2e-b122-638af63538b0',
			'links/wi-dot.jpg',
			'WI DOT Thumbnail'
		);
	?>

	<?php echo $link->links_categories(); ?>
</div>



<div class="link-section" id="streaming-video">
	<h3 class="section-header">Streaming Video</h3>
	<div class="go-to-top"><a href="#">Top &uarr;</a></div>
	
	<?php
		echo $link->links_make_link(
			'Water Pollution Controls While You Work: Temporary BMPs on Highway Construction Sites',
			'',
			'http://www.dot.ca.gov/hq/construc/stormwater/waterpollution.asx',
			'links/ca-dot.jpg',
			'CA DOT Thumbnail'
		);
		
		echo $link->links_make_link(
			'Storm Water Training',
			'',
			'http://www.dot.ca.gov/hq/construc/stormwater/swppp_training.html',
			'links/ca-dot.jpg',
			'CA DOT Thumbnail'
		);
		
		echo $link->links_make_link(
			'Prefabricated Bridge Elements and Systems Video Clips',
			'',
			'http://www.fhwa.dot.gov/bridge/prefab/videos.cfm',
			'links/fhwa.jpg',
			'FHWA Thumbnail'
		);
		
		echo $link->links_make_link(
			'Child Passenger Safety, NJ Division of Highway Traffic Safety',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/Childseats_Woman_v2.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'Pedestrian Safety is a Shared Responsibility, NJ Division of Highway Traffic Safety',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/Ped_Safety_v2.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'Back to School Traffic Safety, NJ Division of Highway Traffic Safety',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/traffic_safety_mp3.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'Cell Phone Law, Avoid Distractions, NJ Division of Highway Traffic Safety',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/cell_phone_law_avoid_distractions.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'NJ Division of Highway Traffic Safety, Safety Campaign, Radio Announcements',
			'',
			'http://www.state.nj.us/lps/hts/library.html',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'NJ Division of Highway Traffic Safety, Safety Campaign, Radio Announcements',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/new08bac.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'NJ Division of Highway Traffic Safety, Safety Campaign, Radio Announcements',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/walk_safely_childern.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'NJ Division of Highway Traffic Safety, Safety Campaign, Radio Announcements',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/walk_safely_childern.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'NJ Division of Highway Traffic Safety, Safety Campaign, Radio Announcements',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/you_drink_you_drive_you_lose.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'NJ Division of Highway Traffic Safety, Safety Campaign, Radio Announcements',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/click_it_or_ticket_en.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'NJ Division of Highway Traffic Safety, Safety Campaign, Radio Announcements',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/safety_shouldnt_take_a_vacation.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'NJ Division of Highway Traffic Safety, Safety Campaign, Radio Announcements',
			'',
			'http://www.state.nj.us/lps/hts/downloads/audio/walk_safely_seniors.mp3',
			'links/nj-hts.jpg',
			'NJ HTS Thumbnail'
		);
		
		echo $link->links_make_link(
			'Motorcycle Awareness/Safety Public Service Announcements',
			'',
			'http://www.dps.state.mn.us/mmsc/latest/MMSCHomeSecondary.asp?cid=4&mid=14',
			'links/mn-dps.jpg',
			'MN DPS Thumbnail'
		);
		
		echo $link->links_make_link(
			"Connecticut DOT Media Library, PSA's, General Info, Research, and Online Videos",
			'',
			'http://www.ct.gov/dot/cwp/view.asp?a=1617&Q=273484&dotNav=|',
			'links/ctdot.jpg',
			'CT DOT Thumbnail'
		);
		
		echo $link->links_make_link(
			'Dynamic Cone Penetrometer (DCP), Soil Strength Testing Video, 10 Min.',
			'',
			'http://www.mrr.dot.state.mn.us/research/DCP/DPC_Operation_28mb.wmv',
			'links/mn-dot.jpg',
			'MN DOT Thumbnail'
		);
		
		echo $link->links_make_link(
			'Truck Roll Over Stability Project, CVO, 1 Min Video, High Bandwidth',
			'',
			'http://www-cta.ornl.gov/cta/Publications/TruckRollover/trucks.wmv',
			'links/ornl.jpg',
			'ORNL Thumbnail'
		);
		
		echo $link->links_make_link(
			"Traffic Signal Management: It's About Time Cost Effective Street Capacity and Safety, 13 Min Video",
			'',
			'http://ops.fhwa.dot.gov/arterial_mgmt/video/its_about_time.wmv',
			'links/fhwa.jpg',
			'FHWA Thumbnail'
		);
	?>

	<?php echo $link->links_categories(); ?>
</div>



<div class="link-section" id="training-opps">
	<h3 class="section-header">Training Opportunities</h3>
	<div class="go-to-top"><a href="#">Top &uarr;</a></div>
	
	<?php
		echo $link->links_make_link(
			'Professional Capacity Building Program Training for ITS',
			'',
			'http://www.pcb.its.dot.gov/calendar.asp',
			'links/rita.jpg',
			'RITA Thumbnail'
		);
		
		echo $link->links_make_link(
			'Transportation Planning Capacity Building Program, Training and Education',
			'',
			'http://www.planning.dot.gov/training.asp',
			'links/planning.jpg',
			'Planning Thumbnail'
		);
		
		echo $link->links_make_link(
			'Distance Learning - Portland Cement Association, Cost Associated',
			'',
			'http://www.cement.org/learn/ln_distance.asp',
			'links/pca.jpg',
			'PCA Thumbnail'
		);
		
		echo $link->links_make_link(
			"NHI National Highway Institute, Distance Learning; Training Without Travel",
			'',
			'http://www.nhi.fhwa.dot.gov/',
			'links/nhi.jpg',
			'NHI Thumbnail'
		);
	?>

	<?php echo $link->links_categories(); ?>
</div>



<div class="link-section" id="listserv">
	<h3 class="section-header">Listservs</h3>
	<div class="go-to-top"><a href="#">Top &uarr;</a></div>
	
	<?php
		echo $link->links_make_link(
			'Listserv - Census CTPP Post',
			'Subscribe by request.',
			'mailto:ctppnews@chrispy.net',
			'links/listserv.png',
			'Listserv Thumbnail'
		);
		
		echo $link->links_make_link(
			'Listserv - Snow-Ice (Winter Maintenance)',
			'Subscribe by contacting Paul Pisano.',
			'mailto:paul.pisano@fhwa.dot.gov',
			'links/listserv.png',
			'Listserv Thumbnail'
		);
		
		echo $link->links_make_link(
			'Listserv - Freight Planning',
			'',
			'http://listserv.utk.edu/archives/fhwafp.html',
			'links/listserv.png',
			'Listserv Thumbnail'
		);
		
		echo $link->links_make_link(
			'Listserv - Kansas Rural Transportation Assistance Program',
			'',
			'http://www.kutc.ku.edu/cgiwrap/kutc/rtap/index.php/listserv.html',
			'links/listserv.png',
			'Listserv Thumbnail'
		);
		
		echo $link->links_make_link(
			'E-Mail List - Bicycle Advocacy Group',
			'',
			'http://probicycle.com/maillist.html',
			'links/listserv.png',
			'Listserv Thumbnail'
		);
		
		echo $link->links_make_link(
			'E-Mail List - Fuel Tax Evasion',
			'Subscribe by request.',
			'mailto:Linda.Morris@fhwa.dot.gov',
			'links/listserv.png',
			'Listserv Thumbnail'
		);
	?>

	<?php echo $link->links_categories(); ?>
</div>



<div class="link-section" id="transportation">
	<h3 class="section-header">Transportation Topics</h3>
	<div class="go-to-top"><a href="#">Top &uarr;</a></div>

	<?php echo $link->links_categories(); ?>
</div>



<div class="link-section" id="web-conferences">
	<h3 class="section-header">Web Conferences</h3>
	<div class="go-to-top"><a href="#">Top &uarr;</a></div>

	<?php echo $link->links_categories(); ?>
</div>