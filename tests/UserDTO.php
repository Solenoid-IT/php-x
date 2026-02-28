<?php



include_once ( __DIR__ . '/../vendor/autoload.php' );



use \Solenoid\X\Data\DTO;

use \Solenoid\X\Data\Types\StringValue;
use \Solenoid\X\Data\Types\IntValue;
use \Solenoid\X\Data\ArrayList;



class BirthDataDTO extends DTO
{
    public function __construct
    (
        #[ StringValue( '', true, 'Date of birth', '/^\d{4}-\d{2}-\d{2}$/' ) ]
        public string $date,

        #[ StringValue( '', true, 'Place of birth' ) ]
        public string $place
    )
    {}
}

class PostDTO extends DTO
{
    public function __construct
    (
        #[ StringValue( '', true, 'Title of the post' ) ]
        public string $title,

        #[ StringValue( '', true, 'Content of the post' ) ]
        public string $content
    )
    {}
}



class UserDTO extends DTO
{
    public function __construct
    (
        #[ StringValue( '', true, 'Name of the user', '/^[\w\ ]+$/' ) ]
        public string $name,

        #[ IntValue( '', true, 'Hierarchy of the user', 1 ) ]
        public int    $hierarchy,

        public BirthDataDTO $birth_data,

        #[ ArrayList( new IntValue( 'id', true, 'ID of the group', 1 ), 1 ) ]
        public array $groups = [],

        #[ StringValue( '', false, 'Phone number of the user', '/^\d+$/' ) ]
        public ?string $phone = null,

        #[ ArrayList( PostDTO::class ) ]
        public array $posts = []
    )
    {}
}



$dto_1 = new UserDTO( 'John Doe', 1, new BirthDataDTO( '1970-01-01', 'Los Angeles' ) );

$dto_2 = UserDTO::import
(
    [
        'name'         => 'John Doe',
        'hierarchy'    => 1,
        'birth_data'   =>
        [
            'date'     => '1970-01-01',
            #'date'     => 12345,
            'place'    => 'Los Angeles'
        ],
        'groups'       => [1, 2, 3],
        'phone'        => null,

        'other_data_1' => 'fake_1',
        'other_data_2' =>
        [
            'fake_1' => 'val_1',
            'fake_2' => 'val_2'
        ],

        'posts'        =>
        [
            [
                'title'      => 'Post 1',
                'content'    => 'This is the content of the first post',
                'fake_field' => 'fake_value'
            ],
            [
                'title'   => 'Post 2',
                'content' => 'This is the content of the second post'
            ]
        ]
    ],

    $errors
)
;



print_r( $dto_2 ?? $errors );

#echo "\n" . $dto_2->get( 'birth_data.place' ) . "\n";

#echo "\n" . $dto_2->get( 'posts' )[1]->get( 'content' ) . "\n";



class UpsertDTO extends DTO
{
    public function __construct
    (
        #[ IntValue( '', false, 'ID of the user', 1 ) ]
        public ?int $id = null,

        #[ StringValue( '', true, 'Name of the user', '/^[\w\ ]+$/' ) ]
        public string $name
    )
    {}
}

$dto_3 = UpsertDTO::import
(
    [
        #'id'   => 1,
        'name' => 'John Doe'
    ],

    $errors
)
;

print_r($errors);



?>