<?php


namespace functional;

use PDO;
use PHPUnit\Framework\TestCase;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Traits\TestDataHelperTrait;

class TestDataHelperSmokeTest extends TestCase
{

   use TestDataHelperTrait;

    function setUp(): void
    {
        parent::setUp();
        $this->getDatabaseFacade()->execute("
            DROP TABLE IF EXISTS  skill;
            
            CREATE TEMPORARY TABLE skill (
              skill_id int unsigned NOT NULL AUTO_INCREMENT,
              name varchar(255),
              is_public tinyint DEFAULT 0,
              created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (skill_id)
            ) 
       ");
        $this->getDatabaseFacade()->execute("
           DROP TABLE IF EXISTS  process;

           CREATE TEMPORARY TABLE process (
              process_id int unsigned NOT NULL AUTO_INCREMENT,
              process_status_id int unsigned DEFAULT 1,
              job_id int unsigned DEFAULT NULL,
              employee_id int unsigned DEFAULT NULL,
              PRIMARY KEY (process_id)
            )
       ");

        $this->getDatabaseFacade()->execute("
           DROP TABLE IF EXISTS  asset;

           CREATE TEMPORARY TABLE asset (
              asset_id int unsigned NOT NULL AUTO_INCREMENT,
              is_active tinyint DEFAULT 1,
              image_url varchar(255),
              created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (asset_id)
            ) 
       ");

        $this->getDatabaseFacade()->execute("
           DROP TABLE IF EXISTS  employee;

           CREATE TEMPORARY TABLE employee (
              employee_id int unsigned NOT NULL AUTO_INCREMENT,
              first_name varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              surname varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              photo_asset_id int unsigned DEFAULT NULL,
              created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (employee_id)
            ) 
       ");

        $this->getDatabaseFacade()->execute("
             DROP TABLE IF EXISTS  employee_skill;

           CREATE TEMPORARY TABLE employee_skill (
              employee_skill_id int unsigned NOT NULL AUTO_INCREMENT,
              skill_id int unsigned DEFAULT NULL,
              employee_id int unsigned DEFAULT NULL,
              created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (employee_skill_id)
            )
       ");


    }

    function test_Example()
    {

        $out = $this->getTestDataHelper()->execute('
            !mysql:mock-time "2020-01-02 19:00:11"
            !set-default-value asset.last_updated 2020-11-02 19:00:11
            !import tables/skills.psv
            !mysql:set-autoincrement skill 900
            
            !set %IMAGE_URL_JOHN image-john
            
            !set %WITH_INTERPOLATION "image-for john {IMAGE_URL_JOHN}"
           
            [process]
            employee_id    |job_id
            *              |*
          
            [asset]
            asset_id                  |image_url                       
            %asset_ID.COVER-JOHN      |> {WITH_INTERPOLATION}
            %asset_ID.COVER-MEG       |this is {PHOTO}
             
             
            [employee]
            employee_id         |first_name |surname       |cover_image_asset_id
            %employee_ID.JOHN   |John       |Barker        |%asset_ID.COVER-JOHN
            %employee_ID.MEG    |Meg        |*             |%asset_ID.COVER-MEG
           
            [employee_skill]
            employee_id        |skill_id
            %employee_ID.MEG   |%SKILL_ID.AD_CAMPAIGNS
            %employee_ID.MEG   |%SKILL_ID.PRODUCTION_MANAGER
            %employee_ID.JOHN  |%SKILL_ID.COPY_WRITING
            
            
            [asset]
            asset_id                  |image_url
            %asset_ID.COVER-JOHN      |http://www.images.com/cover/john.jpg
            
            [asset]
            asset_id                 |image_url
            %asset_ID.COVER-JOE      |http://www.images.com/cover/joe.jpg
            
            [asset]
            asset_id       |image_url
            *              |http://www.images.com/cover/generated.jpg
            %asset_LATEST  |http://www.images.com/cover/after-generated.jpg
             
            [asset]
            asset_id       |image_url
            %asset_LATEST  |*
             
            #!debug-table skill
        
            
        ', ['PHOTO' => 'http://www.images.com/cover/photo.jpg']);


        $this->getDatabaseFacade()->insert("skill", ['name' => 'new']);
        $this->assertTableStateContains("
            
            [skill]
            skill_id   |name                 |is_public   |created_at            |last_updated
            200        |Graphic Design       |NULL                   |2020-01-02 19:00:11   |2020-01-02 19:00:11
            201        |Ad Campaigns         |NULL                   |2020-01-02 19:00:11   |2020-01-02 19:00:11
            202        |Typography           |NULL                   |2020-01-02 19:00:11   |2020-01-02 19:00:11
            203        |Production Manager   |NULL                   |2020-01-02 19:00:11   |2020-01-02 19:00:11
            204        |Illustration         |NULL                   |2020-01-02 19:00:11   |2020-01-02 19:00:11
            205        |Copy Writing         |NULL                   |2020-01-02 19:00:11   |2020-01-02 19:00:11
            900        |new                  |NULL                   |2020-01-02 19:00:11   |2020-01-02 19:00:11
            
            
            [employee_skill]
            skill_id   |employee_id    |created_at            |last_updated
            201        |425002         |2020-01-02 19:00:11   |2020-01-02 19:00:11
            203        |425002         |2020-01-02 19:00:11   |2020-01-02 19:00:11
            205        |425001         |2020-01-02 19:00:11   |2020-01-02 19:00:11
            
            
            [employee]
            employee_id   |first_name |photo_asset_id   |created_at            |last_updated         
            425001        |John       |NULL             |2020-01-02 19:00:11   |2020-01-02 19:00:11  
            425002        |Meg        |NULL             |2020-01-02 19:00:11   |2020-01-02 19:00:11  
            
            
            [asset]
            asset_id            |is_active    |image_url                                          |created_at            |last_updated         
            364000              |1            |http://www.images.com/cover/john.jpg               |2020-01-02 19:00:11   |2020-01-02 19:00:11  
            %asset_ID.COVER-MEG |1            |this is http://www.images.com/cover/photo.jpg      |2020-01-02 19:00:11   |2020-11-02 00:00:00  
            %asset_ID.COVER-JOE |1            |http://www.images.com/cover/joe.jpg                |2020-01-02 19:00:11   |2020-11-02 00:00:00  
            364003              |1            |http://www.images.com/cover/generated.jpg          |2020-01-02 19:00:11   |2020-11-02 00:00:00  
            364004              |1            |image_url-364004                                   |2020-01-02 19:00:11   |2020-01-02 19:00:11  
           
            ", $this->prefixPlaceholders($out));

        $this->assertEquals([
            'PHOTO'                       => 'http://www.images.com/cover/photo.jpg',
            'SKILL_ID.GRAPHIC_DESIGN'     => '200',
            'SKILL_ID.AD_CAMPAIGNS'       => 201,
            'SKILL_ID.TYPOGRAPHY'         => 202,
            'SKILL_ID.PRODUCTION_MANAGER' => 203,
            'SKILL_ID.ILLUSTRATION'       => 204,
            'SKILL_ID.COPY_WRITING'       => 205,
            'asset_ID.COVER-JOHN'         => 364000,
            'asset_ID.COVER-MEG'          => 364001,
            'asset_ID.COVER-JOE'          => 364002,
            'employee_ID.JOHN'            => 425001,
            'employee_ID.MEG'             => 425002,
            'asset_LATEST'                => 364004,
            'WITH_INTERPOLATION'          => 'image-for john image-john',
            'IMAGE_URL_JOHN'              => 'image-john',
        ], $out);

    }

    function test_Example_sequencing_startValue()
    {
        $this->getTestDataHelper()->execute("
            !set-start-value asset 1
            
           
            [asset]
            asset_id                  |image_url
            %asset_ID.COVER-JOHN      |> image-for john %IMAGE_URL_JOHN 
            
            ");
        $this->assertTableStateContains("
             
            [asset]
            asset_id                                        
            1            
            ");
    }

    function test_Example_sequencing_defaultsToGenerated()
    {
        $this->getTestDataHelper()->execute("
            [asset]
            asset_id                  |image_url
            %asset_ID.COVER-JOHN      |> image-for john %IMAGE_URL_JOHN 
            
            ");
        $this->assertTableStateContains("
            [asset]
            asset_id                                        
            364000           
            ");
    }

    function test_Example_moreComplexSequencing()
    {
        $this->getTestDataHelper()->execute("
           
            [asset]
            asset_id                  |image_url
            %asset_ID.COVER-JOHN      |> image-for john %IMAGE_URL_JOHN 
            %asset_ID.COVER-MEG       |%PHOTO
            
            
            #update a row
            
            [asset]
            asset_id                  |image_url
            %asset_ID.COVER-JOHN      |http://www.images.com/cover/john.jpg
           
            
            
            #add a row using new placeholder
            
            [asset]
            asset_id                 |image_url
            %asset_ID.COVER-JOE      |http://www.images.com/cover/joe.jpg
            
            
            
            #add a row with literal key value higher than the last sequence
            
            [asset]
            asset_id                 |image_url
            364010                   |http://www.images.com/cover/yasmin.jpg
            
            #add a row to test sequence to test sequence increases to 364011
            
            
            [asset]
            asset_id                 |image_url
            %asset_ID.COVER-NEW      |http://www.images.com/cover/new_____.jpg
            %asset_ID.COVER-NEW      |http://www.images.com/cover/new.jpg
            
            #add a row with literal key value lower than the last value, this should not update key sequence
            
            [asset]
            asset_id                 |image_url
            50                       |http://www.images.com/cover/low-id.jpg
            
            #this is to verify that sequence does not increase
            
            [asset]
            asset_id                 |image_url
            %asset_ID.COVER-NEW-2    |http://www.images.com/cover/new-2.jpg
             
            #!debug-table asset %asset_ID.NEW_FROM_PLACEHOLDER
           
           
            
            !set %asset_ID.COVER-NEW-3 400000
            
            
            [asset]
            asset_id                 |cloudinary_public_id
            %asset_ID.COVER-NEW-3    |1234
            %asset_ID.COVER-NEW-4    |0000
            
            !set-start-value asset 500000
            
            [asset]
            asset_id                 |cloudinary_public_id
            %asset_ID.COVER-NEW-5    |1111
            
         
        ", ['PHOTO' => 'http://www.images.com/cover/photo.jpg']);


        $this->assertTableStateContains("
             
            [asset]
            asset_id            |is_active    |image_url                              
            50                  |1            |http://www.images.com/cover/low-id.jpg     
            364000              |1            |http://www.images.com/cover/john.jpg          
            %asset_ID.COVER-MEG |1            |http://www.images.com/cover/photo.jpg         
            %asset_ID.COVER-JOE |1            |http://www.images.com/cover/joe.jpg           
            364010              |1            |http://www.images.com/cover/yasmin.jpg     
            %asset_ID.COVER-NEW |1            |http://www.images.com/cover/new.jpg     
            364012              |1            |http://www.images.com/cover/new-2.jpg     
            400000              |1            |NULL
            400001              |1            |NULL
            500000              |1            |NULL
           
            ", $this->prefixPlaceholders($this->getTestDataHelper()->exportPlaceholders()));

        $this->assertEquals(
            [
                'PHOTO'                => 'http://www.images.com/cover/photo.jpg',
                'asset_ID.COVER-JOHN'  => 364000,
                'asset_ID.COVER-MEG'   => 364001,
                'asset_ID.COVER-JOE'   => 364002,
                'asset_ID.COVER-NEW'   => 364011,
                'asset_ID.COVER-NEW-2' => 364012,
                'asset_ID.COVER-NEW-3' => 400000,
                'asset_ID.COVER-NEW-4' => 400001,
                'asset_ID.COVER-NEW-5' => 500000,
            ], $this->getTestDataHelper()->exportPlaceholders());
    }

    function test_AutogeneratedNullColumns()
    {
        $this->getDatabaseFacade()->execute("DROP TABLE IF EXISTS test_main_table");
        $this->getDatabaseFacade()->execute("CREATE TEMPORARY table test_main_table (
            test_main_table_id    INT UNSIGNED NOT NULL     AUTO_INCREMENT PRIMARY KEY,
            name                  varchar(100)         NOT NULL,
            column_tinyint        tinyint UNSIGNED     NOT NULL,
            column_small          smallint UNSIGNED    NOT NULL,
            column_int            int UNSIGNED         NOT NULL,
            column_boolean        tinyint(1)  UNSIGNED NOT NULL,
            column_text           tinytext    NOT NULL
        )

        ");
        $this->getTestDataHelper()->execute("
            !mysql:set-strict-mode
            !set-start-value test_main_table 1000051
            !treat-as-boolean test_main_table column_boolean
        
            [test_main_table]
            test_main_table_id 
            %ID_1       
            %ID_3       
            
            ##automated insert/update depending on if %placeholder in primary_key exists?
         
        ");

        $this->assertTableStateContains("
            [test_main_table]
            test_main_table_id   |name           |column_tinyint   |column_small   |column_int   |column_boolean   |column_text
            1000051              |name-1000051   |197              |17027          |52           |2                |column_text-1000051
            1000052              |name-1000052   |198              |17028          |53           |1                |column_text-1000052
        ");

        $this->getTestDataHelper()->execute("
            [test_main_table]
            test_main_table_id  |column_small
            %ID_1               |90
            
            #!debug-table test_main_table test_main_table_id column_small
        ");

        $this->assertTableStateContains("
           
            [test_main_table]
            test_main_table_id   |column_small
            1000051              |90
            1000052              |17028
        ");
    }

    function test_transactions()
    {
        $this->getTestDataHelper()->execute("
            
            [asset]
            asset_id                  |image_url
            %asset_ID.1               |* 
            *                         |* 
            
            ");

        $this->assertTableStateContains("
            [asset]
            asset_id   |image_url
            364000     |image_url-364000
            364001     |image_url-364001
        
        ");
        $this->assertEquals(
              [
                  'asset' => [
                      [
                          'asset_id' => 364000,
                          'image_url'   => 'image_url-364000'
                      ],
                      [
                          'asset_id' => 364001,
                          'image_url'   => 'image_url-364001'
                      ]
                  ]
              ]
            , $this->getTestDataHelper()->getShadowData()->getData()
        );
        $this->getDatabaseFacade()->beginTransaction();
        $this->getTestDataHelper()->markTransactionStarted();
        $this->getTestDataHelper()->execute("
            [asset]
            asset_id   |image_url
            364000     |http://images.com
            *          |*
        ");
        $this->assertTableStateContains("
            [asset]
            asset_id   |image_url
            364000     |http://images.com
            364001     |image_url-364001
            364002     |image_url-364002
        
        ");
        $this->assertEquals(
              [
                  'asset' => [
                      [
                          'asset_id' => 364000,
                          'image_url'   => 'http://images.com'
                      ],
                      [
                          'asset_id' => 364001,
                          'image_url'   => 'image_url-364001'
                      ],
                      [
                          'asset_id' => 364002,
                          'image_url'   => 'image_url-364002'
                      ]
                  ]
              ]
            , $this->getTestDataHelper()->getShadowData()->getData()
        );
        $this->getTestDataHelper()->markRollback();
        $this->getDatabaseFacade()->rollBack();
        $this->assertTableStateContains("
             
            [asset]
            asset_id   |image_url
            364000     |image_url-364000
            364001     |image_url-364001
            
                 
            ");

        $this->assertEquals(
              [
                  'asset' => [
                      [
                          'asset_id' => 364000,
                          'image_url'   => 'image_url-364000'
                      ],
                      [
                          'asset_id' => 364001,
                          'image_url'   => 'image_url-364001'
                      ]
                  ]
              ]
            , $this->getTestDataHelper()->getShadowData()->getData()
        );

        $this->getTestDataHelper()->execute("
            [asset]
            asset_id      |image_url
            %asset_NEW    |*
        
        ");
        $this->assertTableStateContains("
             
            [asset]
            asset_id   |image_url
            364000     |image_url-364000
            364001     |image_url-364001
            364003     |image_url-364003
            
                 
            ");

    }

    protected function getPdo(): Pdo
    {
        $serverName = $_SERVER['DB_SERVER'] ?? throw new \BadFunctionCallException("SERVER needs to be set");
        $dbName     = $_SERVER['DB_NAME'] ?? throw new \BadFunctionCallException("DB needs to be set");
        $userName   = $_SERVER['DB_USER'] ?? throw new \BadFunctionCallException("USER needs to be set");
        $password   = $_SERVER['DB_PASSWORD'] ?? throw new \BadFunctionCallException("PASSWORD needs to be set");

        $pdo = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    protected function initTestDataHelper(): void
    {
        $this->testDataHelper->execute('
                !set-system config.asset-path tests/assets
                !mysql:disable-foreign-key-checks
                !mysql:set-strict-mode
            ');
    }
}