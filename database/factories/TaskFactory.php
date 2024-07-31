<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\User;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $jobs = 
        [
            [
                "unit" => "FOOD TOXICOLOGY",
                "instructions" => 
                    "a) Discuss the historical development of food toxicology from our ancestors to the modern day <br>
                    b) Describe the nature of chemical contaminant originating from industrial waste production"
                ,
                "length" => 500
            ] ,
            [
                "unit" => "FOOD TOXICOLOGY",
                "instructions" => 
                    "a) Discuss phase I and phase II reactions of biotransformation 
                    <br>
                    b) By giving examples describe classification of toxicants based on chemical used classes"
                ,
                "length"=> 650
            ],
            [
                "unit" => "FOOD TOXICOLOGY",
                "instructions" => 
                "a) Describe any FOUR natural plant toxicants in foods and the suitable methods of reduction in foods
                <br>
                b) Discuss FOUR factors that affect the toxicity of a substance to an organism"
                ,
                "length"=> 550
            ],
            [
                "unit" => "NUTRITION AND HEALTH",
                "instructions" => 
                    "1. Describe four (4) classes of hospital diets. <br>
                    2. Outline seven (7) indications for nutritional assessment. <br>
                    3. Highlight five (5) functions of phospholipids <br>
                    4. Describe the domains of nutrition diagnosis. "
                ,
                "length"=> 600
            ],
            [
                "unit" => "NUTRITION AND HEALTH",
                "instructions" => 
                    "Cardiovascular disorders are among the leading causes of morbidity globally <br>
                    A) Highlight five (5) dietary changes to prevent cardiovascular disease <br>
                    B). Describe meal planning and exchange list in management of cardiovascular diseases. "
                ,
                "length"=> 1200
            ],
            [
                "unit" => "NUTRITION AND HEALTH",
                "instructions" => 
                    " Describe diet exchange list in the management of Diabetes Mellitus "
                ,
                "length"=> 1000
            ],
            [
                "unit" => "MEDICAL PHYSIOLOGY",
                "instructions" => 
                    " (a) Draw an oxyhemoglobin dissociation curve and label the axes <br>
                    (b) Explain the concept of positive cooperativity with in regard to oxygen-hemoglobin
                    dissociation curve <br>
                    2. Write briefly on the definition, cause of and response to metabolic alkalosis <br>
                    3. State six (6) differences between cortical and juxtamedullary nephrons <br>
                    4. State five (5) main functions of the uterus <br>
                    5. Outline four (4) types of hypoxia <br>
                    6. Explain the three (3) phases of gastric acid secretion <br>
                    7. Write short notes on juxtaglomerular apparatus "
                ,
                "length"=> 1700
            ],
            [
                "unit" => "MEDICAL PHYSIOLOGY",
                "instructions" => 
                    "Describe the cardiac physiology under the following subheadings:<br>
                    a) Using a well labelled diagram, illustrate the action potential of a cardiac muscle <>br
                    b) Conducting system of the heart <>br
                    c) Renin-Angiotensin-Aldosterone system (RAAS) "
                ,
                "length"=> 1700
            ],
            [
                "unit" => "MEDICAL PHYSIOLOGY",
                "instructions" => 
                    "Write an essay on reproductive physiology under the following headings: <br>
                    a) Biosynthetic pathway for testosterone synthesis in the testes <br>
                    b) Phases of female sexual cycle (Menstrual Cycle) "
                ,
                "length"=> 2000
            ],
            [
                "unit" => "MEDICAL SURGICAL NURSING",
                "instructions" => 
                    "1. State 6 health messages the nurse should share with a patient post cataract extraction and
                    lens replacement <br>
                    2. Explain the procedure of ear syringing in management of wax impaction <br>
                    3. State 5 clinical manifestations of sinusitis "
                ,
                "length"=> 800
            ],
            [
                "unit" => "MEDICAL SURGICAL NURSING",
                "instructions" => 
                    "Explain the chain of survival in the basic life support <br>
                    State 6 health messages shared with a patient after stitching a wound <br>
                    Outline 5 roles of a post-anesthetic nurse <br>
                    State 5 ways of infection prevention in an ICU setting"
                ,
                "length"=> 1100
            ],
            [
                "unit" => "MEDICAL SURGICAL NURSING",
                "instructions" => 
                    "You are nursing a patient who has lost his sight; <br>
                    A. State 3 types of adaptation necessary for the patient to coop with blindness
                    <br>
                    B. Describe how the nurse will assist this patient spatial orientation and mobility
                    <br>
                    C. Explain the current efforts by ophthalmologists towards visual restoration for
                    the blind "
                ,
                "length"=> 1200
            ],
            [
                "unit" => "MEDICAL SURGICAL NURSING",
                "instructions" => 
                    "Dialysis is becoming a common practice because of increase of renal failure patient
                    numbers. <br>
                    A. Explain 2 renal function tests done to diagnose renal failure<br>
                    B. State 4 nursing interventions implemented after kidney biopsy <br>
                    C. Describe the procedure of hemodialysis "
                ,
                "length"=> 1200
            ],
            [
                "unit" => "MEDICAL SOCIOLOGY AND ANTHROPOLOGY",
                "instructions" => 
                    "1. Outline seven (7) importance of medical sociology and anthropology in nursing <br>
                    2. Differentiate between ascribed and achieved status and give an example of each<br>
                    3. Explain the four components of culture <br>
                    4. Family is a major social institution. Describe four functions of a family <br>
                    5. State two (2) types of cultural anthropology <br>
                    6. State five characteristics (5) of social change <br>
                    7. State four (4) dimensions that commonly appear in the racial socialization literature  "
                ,
                "length"=> 1350
            ],
            [
                "unit" => "MEDICAL SOCIOLOGY AND ANTHROPOLOGY",
                "instructions" => 
                    "Describe a social group under the following subheadings; <br>
                    a) Characteristics of a social group<br>
                    b) Stages of group development according to Bruce Tuckman <br>
                    c) Differences between primary and secondary social groups  "
                ,
                "length"=> 1550
            ],
            [
                "unit" => "MEDICAL SOCIOLOGY AND ANTHROPOLOGY",
                "instructions" => 
                    "The family has undergone some radical changes over the past half-century <br>
                    a) Outline six (6) general characteristics of a family<br>
                    b) Describe seven (7) factors that have affected the stability of a family  "
                ,
                "length"=> 1500
            ],
            [
                "unit" => "MEDICAL SOCIOLOGY AND ANTHROPOLOGY",
                "instructions" => 
                    "1. Discuss Diffusionism theory as it is applies in Anthropology (<br>
                    2. Describe the Subject Matter and Scope of Medical Anthropology (<br>
                    3. Explain Four (4) Modes of Social Learning <br>
                    4. Difference (Five differences) between Repressive and Participatory socialization <br>
                    5. State Five (5) benefits of Social Groups <br>
                    6. State Two (2) causes of gender stratification  "
                ,
                "length"=> 2000
            ],
            [
                "unit" => "MEDICAL SOCIOLOGY AND ANTHROPOLOGY",
                "instructions" => 
                    " Discuss social Pathology under the following headings: <br>
                    a. Define social pathology <br>
                    b. Describe Three (3) causes of Pathology <br>
                    c. Discuss the role of a nurse in fighting corruption in Kenya  "
                ,
                "length"=> 2100
            ],
            [
                "unit" => "MEDICAL SOCIOLOGY AND ANTHROPOLOGY",
                "instructions" => 
                    "Discuss Anthropology of religion under the following headings: <br>
                    a. Define Anthropology of religion <br>
                    b. State Nine (9) elements of Religion <br>
                    c. Discuss the role of religion in the health and sickness of a society "
                ,
                "length"=> 2200
            ],
            [
                "unit" => "MEDICAL SOCIOLOGY AND ANTHROPOLOGY",
                "instructions" => 
                    "Discuss Anthropology of religion under the following headings: <br>
                    a. Define Anthropology of religion <br>
                    b. State Nine (9) elements of Religion <br>
                    c. Discuss the role of religion in the health and sickness of a society "
                ,
                "length"=> 2200
            ],
            [
                "unit" => "INTRODUCTION TO MANAGEMENT AND ORGANIZATION",
                "instructions" => 
                    "1. What are the relative merits of the network and autonomy forms of management? <br>
                    2. Critically assess how digital transformation might impact on these management forms? "
                ,
                "length"=> 1800
            ],
            [
                "unit" => "WIRELESS AND MOBILE DEVICE FORENSICSINTRODUCTION TO MANAGEMENT AND ORGANIZATION",
                "instructions" => 
                    "a) Describe FOUR challenges to a mobile device forensic investigator. <br>
                    b) Discuss FOUR steps in conducting a mobile forensic investigation. <br>
                    c) List FIVE examples of digital evidence that may be found in a mobile device.                    <br>
                    d) During a digital forensic investigation, the scene of the digital crime requires that it is
                    secured from any form of intrusion and change. Describe FOUR processes that are
                    applied to preserve the scene of a digital forensic investigation. <br>
                    e) State FIVE reasons why it is important "
                ,
                "length"=> 2000
            ],
            [
                "unit" => "WIRELESS AND MOBILE DEVICE FORENSICSINTRODUCTION TO MANAGEMENT AND ORGANIZATION",
                "instructions" => 
                    "a) Describe SIX types of methods for mobile digital forensics. <br>
                    b) During the initial stages of an investigation, a forensic expert may be required to visit
                    a site where digital evidence is supposed to be gathered. List FOUR methods of
                    isolation that can be applied to a mobile device during this stage.<br>
                    c) When a mobile device is not properly isolated during the initial stages of an
                    investigation, this leaves room for a suspect to make certain modiifications to such a
                    device. Describe FOUR possible modifications to such a device. "
                ,
                "length"=> 2200
            ],
            [
                "unit" => "WIRELESS AND MOBILE DEVICE FORENSICSINTRODUCTION TO MANAGEMENT AND ORGANIZATION",
                "instructions" => 
                    "a) Describe any FOUR tools that can be used to conduct mobile forensics investigations.<br>
                    b) Describe THREE roles of mobile forensics investigations in the current era of
                    digitalization. <br>
                    c) According to a recent study about the state of Kenya`s cyber security in 2020, it was
                    reported that online fraud grew by 529%. Discuss examples of online activities that
                    could be considered fraudulent. "
                ,
                "length"=> 1800
            ],
            [
                "unit" => "MEDIA AND CRIME",
                "instructions" => 
                    "a) With reference to Bandura’s Bobo Dolls study, discuss the notion that media provide stimuli for
                    criminal behaviour. <br>
                    b) Media can inflict secondary victimization on some victims by worsening their pain and suffering but
                    there are also some positive aspects of media coverage of crime. Using clear examples, discuss this
                    statement. <br>
                    c) Using clear examples, explain the following concepts in relation the construction of crime news: <br>
                    <ol>
                    <li>Public appeal</li>
                    <li>Public interest</li> 
                    </ol>"
                ,
                "length"=> 1500
            ],
            [
                "unit" => "MEDIA AND CRIME",
                "instructions" => 
                    "a) Using a clear and well-labeled diagram, explain the media social construction process. <br>
                    b) Briefly explain how media portrayals of corrections in Kenya affect perceptions of the prison
                    service in the country"
                ,
                "length"=> 1000
            ],
            [
                "unit" => "MEDIA AND CRIME",
                "instructions" => 
                    "Using Strain Theory, discuss the notion that media propagate anomie through glorified portrayals of
                    wealth and affluence."
                ,
                "length"=> 1200
            ],
            [
                "unit" => "MEDIA AND CRIME",
                "instructions" => 
                    "a) New media have been useful and at the same time posed a challenge to law enforcement. Using
                    clear examples, explain this statement  <br>
                    b) Explain the following news values as applied in reporting crime news: <br>
                    <ol>
                    <li>Children</li>
                    <li>Celebrity</li>
                    <li>Violence</li>
                    <li>Predictability</li>
                    <li>Individualism</li>
                    </ol>"
                ,
                "length"=> 1500
            ],
            [
                "unit" => "INFORMATION SECURITY",
                "instructions" => 
                    "e) A university has experienced an attack that has already been classified as information security incident. In
                    executing its Incidence Response Plan, the university has requested your professional input as a
                    trained information security management office. <br>
                    <ol>
                    <li> Discuss the actions that you will take during and after the incidence. </li>
                    <li> Analyse any three behaviours that the University may have observed on the attack before
                    classifying it as an information security incidence.</li>
                    <li> Demonstrate any four the strategies that you will employ to help the University stop the
                    incident and recover control of its system </li>
                    </ol> <br>
                    f) Analyse the difference between a cryptogram and a Cryptosystem."
                ,
                "length"=> 1700
            ],
            [
                "unit" => "INFORMATION SECURITY",
                "instructions" => 
                    "As the chief information security officer for your organization, you are to lead a disaster recovery
                    team in preparation for and recovery from a disaster that has imparted the organisation’s
                    information systems infrastructure. Develop a sample disaster recovery plan that you will use for
                    this exercise."
                ,
                "length"=> 700
            ],
            [
                "unit" => "INFORMATION SECURITY",
                "instructions" => 
                    "a) Evaluate the importance of the C.I.A. triad and explain each of its components. <br>
                    b) As a qualified information security officer, you have been hired by XYZ Corporation to design an
                    Information security management system for them. Discuss the two distinct phases that will
                    constitute your design process. <br>
                    c) Contingency planning (CP) is how organizational planners position their organizations to prepare
                    for, detect, react to, and recover from events that threaten the security of information resources
                    and assets. <br>
                    <ol>
                    <li> Discuss the main goal of CP. </li>
                    <li> Explain the three components of a CP </li>
                    </ol> 
                    d) Give an example for each of the following access control mechanisms <br>
                    <ol>
                    <li> Something you know </li>
                    <li> Something you have </li>
                    <li) Something you are </li>
                    <ol>"
                ,
                "length"=> 700
            ],
            [
                "unit" => "INTERNATIONAL MANAGEMENT",
                "instructions" => 
                    "Imagine you are a manager working in the Corporate Social Responsibility (CSR) division of a large
                    European business in the Clothing and Fashion Sector. Your supervisor allocates you a new work
                    responsibility to support the expansion of operations to find new sources of raw materials and
                    outsource manufacturing to countries located in the Global South. The Director of CSR has asked you to
                    prepare a document* in which you should to clarify the organizational rationale for CSR, identify the
                    potential risks and opportunities for implementing CSR throughout the new value chain arrangements.
                    You must also include within the document recommendations of how you will overcome any potential
                    threats associated with the expansion. "
                ,
                "length"=> 3000
            ],
            [
                "unit" => "INTERNATIONAL MANAGEMENT",
                "instructions" => 
                    "Imagine you are a manager working in the Human Resources Division of an international organization
                    headquartered in the Global North. You are appointed to support the launch of a new subsidiary which
                    is being set up in the Global South. The CEO has asked you to prepare a document* that outlines the
                    International HR policy and practice needed to ensure the project is a success. The document should
                    explain the potential challenges to design and deliver appropriate recruitment, selection, training and
                    on-the job practices relating to the managerial and technical employees who will be sent overseas to
                    staff senior positions in the new subsidiary. You must also include within the document
                    recommendations of how you will overcome any potential challenges with reference to theory and
                    practice. "
                ,
                "length"=> 3000
            ],
            [
                "unit" => "INTERNATIONAL MANAGEMENT",
                "instructions" => 
                    "Imagine you are a manager working in the Diversity and Equality Division of an international
                    organization. You are appointed to support the launch of a new initiative to support equality between
                    male and female staff in relation to career progression throughout the organization. The CEO has
                    asked you to prepare a document* that outlines the policy and practice needed to ensure improved
                    gender equality in career progression amongst managerial and technical staff. The document should
                    explain the potential challenges people face including possible gendered barriers within and outside the
                    organization. You must also include within the document recommendations of how you will create
                    polices and establish practices to help staff overcome such barriers and in doing so refer to theory and
                    practice.
                        "
                ,
                "length"=> 3000
            ],
            [
                "unit" => "INTERNATIONAL MANAGEMENT",
                "instructions" => 
                    "Imagine you are a leader within a global organisation and you have been asked to prepare a document
                    for the Board of Directors of the organisation on 'How do we need to lead teams virtually/ physically
                    and in a hybrid way in the new world of work?' Prepare a document* that would help them
                    understand the challenges and the suggested actions. Your paper would need to describe some of the
                    factors that have significantly changed in the last two years that impact on the world of work globally
                    drawing attention to the role of managing the tension between physical space, virtual space and the
                    hybrid space. Describe what principles, processes and policies would need to be put in place to
                    facilitate managing these tensions. Identify any potential obstacles that impact on time and space
                    issues and how these may be overcome. In evaluating the answer to the question, critical analysis and
                    reflection of factors supported by theory and practice will be a key consideration."
                ,
                "length"=> 3000
            ],
        ];
        $page_cost = rand(150,450);
        $pages = rand(1,20);
        return [
            'topic' => $this->faker->name(),
            'unit' => $jobs[Floor(rand(1, 34))]['unit'],
            'type' => 'Article',
            'instructions' => $jobs[Floor(rand(1, 34))]['instructions'],
            'broker_id' => $this -> getBrokerId(),
            'pages' =>  $pages,
            'page_cost' => $page_cost,
            'expiry_time' => Carbon::now()->addMinutes(rand(360, 7200))->toDateTimeString(),
            'full_pay' => $pages * $page_cost,
            'pay_day' => $this -> fakePayDay(),
            'difficulty' => Floor(rand(1,9)),
            'verified_only' => (Floor(rand(1,9)) > 2) ? true : false,
            'status' => Floor(rand(0,5)),
            'code' => $this->fakeCode(),
        ];
    }

    public $jobs;

    public function getBrokerId()
    {
        $users = User::all();
        $user_count = count($users);
        $user = $users[Floor(rand(0, ($user_count - 1)))];
        return $user -> broker -> id;
    }

    public function fakeUnit(){
        $units = array('Calculus', 'Data Analysis', 'Political Science', 'Steel Structures', 'Applied Mathematics', 'Microbiology', 'Law', 'Physics', 'Analytical chemistry', 'Python');
        $random_number = floor(rand(0, (count($units) - 1 )));
        return $units[$random_number];
    }

    public function fakeType(){
        $types = array('Essay', 'Report', 'Trascription', 'Article', 'Programming');
        $random_number = floor(rand(0, (count($types) - 1 )));
        return $types[$random_number];
    }

    public function fakePayDay(){
        $base = floor(rand(1,4));
        switch ($base) {
            case '1':
                //on approval
                return '1997-9-17 00:00:00';
                break;
            case '2':
                //on delivery
                return '1965-5-28 00:00:00';
                break;
            
            default:
                //today+>5days - 14 days
                return Carbon::now()->addMinutes(rand(7200, 20160))->toDateTimeString();
                break;
        }
    }

    public function fakeCode(){
        return strtoupper(Str::random(rand(3,5))) . '-' . strtoupper(Str::random(rand(3,6))) . rand(45,123);
    }

    public function Topic(){}
}
