<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends BaseFixture
{
    private $passwordEncoder, $prenumeRomanesti;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->prenumeRomanesti = [
            'David', 'Alexandru', 'Gabriel', 'Ştefan', 'Ana',
            'Ionuţ', 'Mihai', 'Cristian', 'Darius', 'Daniel',
        ];
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_users', function ($i) use ($manager) {
            $user = new User();
            $user->setEmail(sprintf('utilizator%d@deexemplu.com', $i));
            $user->setFirstName($this->prenumeRomanesti[$i]);
            $user->agreeToTerms();

            if ($this->faker->boolean) {
                $user->setTwitterUsername($this->faker->userName);
            }
            if ($this->faker->boolean(75)) {
                $user->setSubscribeToNewsletter(true);
            }

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'engage'
            ));

            $apiToken1 = new ApiToken($user);
            $apiToken2 = new ApiToken($user);
            $manager->persist($apiToken1);
            $manager->persist($apiToken2);

            return $user;
        });

        $user = new User();
        $user->setEmail('favianioel@gmail.com');
        $user->setFirstName('Favian');
        $user->setRoles(['ROLE_ADMIN']);
        $user->agreeToTerms();

        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'suntmaredeveloper'
        ));
        $manager->persist($user);
        $manager->flush();
    }
}
