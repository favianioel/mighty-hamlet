<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Service\UploaderHelper;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class ArticleFixtures extends BaseFixture implements DependentFixtureInterface
{
    private static $articleTitles = [
        'Hamlet: A Love Story',
        'Hamlet and revenge',
        'Polysemic Language, Democratization, and the Empowerment.',
        'And I of Ladies Most Deject and Wretched',
    ];

    private static $articleImages = [
        'sword.jpg',
        'hamletret.jpg',
        'female.jpg',
        'hamlet.jpg'
    ];

    private $uploaderHelper;

    public function __construct(UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_articles', function ($count) use ($manager) {
            $article = new Article();
            $article->setTitle($this->faker->randomElement(self::$articleTitles))
                ->setContent(<<<EOF
Around 1905 or 1906, Sigmund Freud wrote an essay, unpublished in his lifetime, called “Psychopathic Characters on the Stage.” The essay addressed the question of what we, as spectators, get out of watching people go crazy. Freud’s theory was that we’re fascinated by crazy characters because they help us express our own repressed impulses. Drama, of course, can’t express our fantasies too literally; when that happens, we call it pornography and walk out of the theatre. Instead, a good playwright maneuvers our desires into the light using a mixture of titillation and censure, fantasy and irony, obscenity and euphemism, daring and reproach. A good play, Freud wrote, provokes “not merely an enjoyment of the liberation but a resistance to it as well.” That resistance is key. It lets us enjoy our desires without quite admitting that they’re ours.

“Hamlet,” Freud thought, best exemplified the appeal of managed self-expression. Watching “Hamlet,” we think that it’s about revenge—a familiar, safe subject. In fact, “Hamlet” is about desire. The real engine of the play is Oedipal. Caught up in Hamlet’s quest to kill Claudius—and reassured by his self-censure—we can safely, and perhaps unconsciously, explore those desires. Freud thought that prudery and denial had for centuries prevented critics from acknowledging the play’s propulsive undercurrent, which, he believed, the new psychoanalytic vocabulary made it possible to acknowledge. “The conflict in ‘Hamlet’ is so effectively concealed,” he wrote, “that it was left to me to unearth it.”

Freud’s hilarious (and no doubt self-conscious) boast is doubly resonant in “Stay, Illusion!,” the thoughtful, fascinating, and difficult new book about “Hamlet,” by Simon Critchley and Jamieson Webster. Critchley, a philosopher at the New School, and Webster, a psychoanalyst, can’t help but thrill to Freud’s “delightfully arrogant assertion”: they are, after all, writing a book about “Hamlet,” and you only do that if you believe that nearly every great thinker in Western literature has gotten it wrong. At the same time, they resist the idea that “the Oedipus complex provides the definitive interpretation of ‘Hamlet.’ ” Critchley and Webster, a married couple, have clearly been conducting a long-running two-person seminar on “Hamlet.” They call their book the “late-flowering fruit of a shared obsession.” Their book convenes a sort of literary-philosophical-psychoanalytic roundtable—featuring Hegel, Nietzsche, Benjamin, Joyce, and Lacan, among others—to question Freud’s interpretation.

Desire and its repression, they conclude, might be too small a frame for “Hamlet.” It’s better to think about the play in terms of love and its internal contradictions. They argue that we tell the story wrong when we say that Freud used the idea of the Oedipus complex to understand “Hamlet.” In fact, it was the other way around: “Hamlet” helped Freud understand, and perhaps even invent, psychoanalysis. The Oedipus complex is a misnomer. It should be called the Hamlet complex.

Critchley and Webster are proud as well as nervous about the fact that they’re “outsiders to the world of Shakespeare criticism.” “What is staged in ‘Hamlet,’ ” they write, “touches very close to the experience of being a psychoanalyst, that is, someone who has to listen to patients day after day, hour after hour.” Rather than get caught up in the “game of scholarship and interpretation,” their plan is to “cup [their] ear”—that is, to attend to and elaborate on the themes that the play obsesses about. Nothingness is one of those themes; it comes up over and over in the text of the play. (Ophelia to Hamlet: “You are naught, you are naught.” Hamlet to himself: “How weary, stale, flat, and unprofitable / Seem to me all the uses of this world!”) Is “Hamlet,” they wonder, “a nihilist drama”? Love or, more accurately, the failure to love is also a theme. Shame is another. (“For us,” they write, “at its deepest, this is a play about shame.”)
EOF
                );

            // publish most articles
            if ($this->faker->boolean(70)) {
                $article->setPublishedAt($this->faker->dateTimeBetween('-2 weeks', '-1 days'));
            }

            $imageFilename = $this->fakeUploadImage();

            $article->setAuthor($this->getRandomReference('main_users'))
                ->setHeartCount($this->faker->numberBetween(5, 100))
                ->setImageFilename($imageFilename);

            $tags = $this->getRandomReferences('main_tags', $this->faker->numberBetween(0, 5));
            foreach ($tags as $tag) {
                $article->addTag($tag);
            }

            return $article;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TagFixture::class,
            UserFixture::class,
        ];
    }

    private function fakeUploadImage(): string
    {
        $randomImage = $this->faker->randomElement(self::$articleImages);
        $fs = new Filesystem();
        $targetPath = sys_get_temp_dir() . '/' . $randomImage;
        $fs->copy(__DIR__ . '/images/' . $randomImage, $targetPath, true);

        return $this->uploaderHelper
            ->uploadArticleImage(new File($targetPath), null);
    }
}
