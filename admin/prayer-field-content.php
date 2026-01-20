<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

//wp i18n make-pot . languages/default.pot --skip-audit --subtract="languages/terms-to-exclude.pot"

class P4_Ramadan_2026_Content {

    public static function install_content( $language = 'en_US', $names = [], $from_translation = null, $campaign_id = null ) {
        $campaign = DT_Campaign_Landing_Settings::get_campaign( $campaign_id );
        if ( empty( $campaign ) ) {
            dt_write_log( 'Campaign not set' );
            return false;
        }
        $start = $campaign['start_date']['formatted'] ?? '';
        if ( empty( $start ) ) {
            dt_write_log( 'Start date not set' );
            return false;
        }

        $installed = [];
        $content = self::content( $language, $names, $from_translation ?? $language );
        foreach ( $content as $i => $day ) {

            $title = DT_Time_Utilities::display_date_localized( strtotime( $start . ' + ' . $i . ' day' ), $language );

            $slug = str_replace( ' ', '-', strtolower( gmdate( 'F j Y', strtotime( $start . ' + ' . $i . ' day' ) ) ) );
            $post_content = implode( '', wp_unslash( $day['content'] ) );

            $args = [
                'post_title'    => $title,

                'post_content'  => $post_content,
                'post_excerpt'  => $day['excerpt'],
                'post_type'  => 'landing',
                'post_status'   => 'publish',
                'post_author'   => get_current_user_id(),
                'meta_input' => [
                    'prayer_fuel_magic_key' => $slug,
                    'post_language' => $language,
                    'day' => $i + 1,
                    'fuel_tag' => 'ramadan_2026',
                    'linked_campaign' => $campaign['ID'],
                ]
            ];

            $installed[] = wp_insert_post( $args );

        }

        return $installed;
    }

    public static function bullet_list_to_html( $message ){
        //https://stackoverflow.com/questions/2344563/a-regex-that-converts-text-lists-to-html-in-php
        // Normalize multiple newlines before bullet lists to single newline
        $message = preg_replace( '/\n\n+(?=-)/', "\n", $message );
        $message = preg_replace( '/^-+(.*)?/im', '<ul><li>$1</li></ul>', $message );
        return preg_replace( '/(<\/ul>\n(.*)<ul>*)+/', '', $message );
    }

    public static function ramadan_format_message( $message, $fields = [] ) {
        $message = make_clickable( $message );
//            $message = str_replace( '[in location]', !empty( $fields['in_location'] ) ? $fields['in_location'] : '[in location]', $message );
//            $message = str_replace( '[of location]', !empty( $fields['of_location'] ) ? $fields['of_location'] : '[of location]', $message );
//            $message = str_replace( '[location]', !empty( $fields['location'] ) ? $fields['location'] : '[location]', $message );
//            $message = str_replace( '[people_group]', !empty( $fields['ppl_group'] ) ? $fields['ppl_group'] : '[people_group]', $message );
        $message = self::bullet_list_to_html( $message );
        return nl2br( $message );
    }

    public static function format_scripture_passages( $content, $fields = [] ) {
        $output = '';
        $passages = preg_split( '/\n\n+/', trim( $content ) );

        foreach ( $passages as $passage ) {
            $lines = explode( "\n", $passage, 2 );
            $reference = trim( $lines[0] );
            $text = isset( $lines[1] ) ? trim( $lines[1] ) : '';

            $output .= '<!-- wp:heading {"level":4} -->';
            $output .= '<h4>' . wp_kses_post( self::ramadan_format_message( $reference, $fields ) ) . '</h4>';
            $output .= '<!-- /wp:heading -->';

            if ( !empty( $text ) ) {
                $output .= '<!-- wp:paragraph -->';
                $output .= '<p>' . wp_kses_post( self::ramadan_format_message( $text, $fields ) ) . '</p>';
                $output .= '<!-- /wp:paragraph -->';
            }
        }

        return $output;
    }

    public static function content( $language, $names, $from_translation = 'en_US' ) {

        $fields = $names;
        add_filter( 'determine_locale', function ( $locale ) use ( $from_translation ) {
            if ( ! empty( $from_translation ) ) {
                return $from_translation;
            }
            return $locale;
        }, 1001, 1 );
        if ( $from_translation !== 'en_US' ){
            load_plugin_textdomain( 'ramadan-2026', false, trailingslashit( dirname( plugin_basename( __FILE__ ), 2 ) ) . 'languages' );
        }

        $data = [
            //day 1
            [
                __( 'Day 1: Creation', 'ramadan-2026' ),
                __( 'Genesis 1:1-25
In the beginning God created the heaven and the earth. And the earth was without form, and void; and darkness was upon the face of the deep. And the Spirit of God moved upon the face of the waters. And God said, Let there be light: and there was light. And God saw the light, that it was good: and God divided the light from the darkness. And God called the light Day, and the darkness he called Night. And the evening and the morning were the first day. And God said, Let there be a firmament in the midst of the waters, and let it divide the waters from the waters. And God made the firmament, and divided the waters which were under the firmament from the waters which were above the firmament: and it was so. And God called the firmament Heaven. And the evening and the morning were the second day. And God said, Let the waters under the heaven be gathered together unto one place, and let the dry land appear: and it was so. And God called the dry land Earth; and the gathering together of the waters called he Seas: and God saw that it was good. And God said, Let the earth bring forth grass, the herb yielding seed, and the fruit tree yielding fruit after his kind, whose seed is in itself, upon the earth: and it was so. And the earth brought forth grass, and herb yielding seed after his kind, and the tree yielding fruit, whose seed was in itself, after his kind: and God saw that it was good. And the evening and the morning were the third day. And God said, Let there be lights in the firmament of the heaven to divide the day from the night; and let them be for signs, and for seasons, and for days, and years: And let them be for lights in the firmament of the heaven to give light upon the earth: and it was so. And God made two great lights; the greater light to rule the day, and the lesser light to rule the night: he made the stars also. And God set them in the firmament of the heaven to give light upon the earth, And to rule over the day and over the night, and to divide the light from the darkness: and God saw that it was good. And the evening and the morning were the fourth day. And God said, Let the waters bring forth abundantly the moving creature that hath life, and fowl that may fly above the earth in the open firmament of heaven. And God created great whales, and every living creature that moveth, which the waters brought forth abundantly, after their kind, and every winged fowl after his kind: and God saw that it was good. And God blessed them, saying, Be fruitful, and multiply, and fill the waters in the seas, and let fowl multiply in the earth. And the evening and the morning were the fifth day. And God said, Let the earth bring forth the living creature after his kind, cattle, and creeping thing, and beast of the earth after his kind: and it was so. And God made the beast of the earth after his kind, and cattle after their kind, and every thing that creepeth upon the earth after his kind: and God saw that it was good.', 'ramadan-2026' ),
                __( 'God is a powerful, joyful Creator who brings order from chaos, speaks with authority, reveals Himself to be known, and multiplies His goodness through His creation and His people.

- Pray for people in this place to see and celebrate God\'s creativity, goodness, and the order He brings into the world and into our lives.
- Pray for God to raise up faithful partners in His mission among this people, reflecting his character and making his story known to the nations.', 'ramadan-2026' ),
                __( 'Humanity is invited into God\'s generous, relational design—not as the center of the story, but as participants who receive His abundance, reflect His community, and live within the order He lovingly created.

- Pray for God to teach people in this place to remember that we are not the main characters, but grateful participants in God\'s greater story.
- Ask God to grow hearts among this people to live out of the generosity He has shown—sharing, multiplying, and stewarding what He entrusts to us.', 'ramadan-2026' ),
                __( 'This story reveals that God only creates good. He doesn\'t create evil, contrary to Islamic doctrine. According to Islamic teaching, Satan was originally a jinn, created by God from smokeless fire. Islam also teaches that God is unknowable, but here we see Him revealing Himself, giving us important information to know about Him. This difference in understanding impacts how Muslims respond to Biblical accounts of nature.

Recovering from metastatic cancer and having finished radiation, Hanen longs to hope in God\'s power to heal and save. Listening to the story of Creation stirred her heart to praise God\'s strength. Just a few days later, however, her initial openness gave way to resistance when she adamantly declared that healing can only come through reading the Quran and that she doesn\'t need to read God\'s Holy Word from the Torah (Old Testament) and Injil (New Testament).

Pray for God to stir a hunger for reading God\'s Word from the Bible and for fear to be replaced with faith.', 'ramadan-2026' ),
                __( 'Most Muslims believe God created the world, though they don\'t regard the Bible\'s account as accurate.

- Pray for Christians trying to share Good News with Muslims to confidently invite Muslims to hear this story and to discuss it with others.
- Pray that a curiosity would be stirred to want to continue to read more stories.', 'ramadan-2026' ),
                __( 'Colossians 1:15-22
Jesus, you are the image of the invisible God, the firstborn of all creation. By you, all things were created in heaven and on earth, visible and invisible, whether thrones or dominions or rulers or authorities. All things were created through you and for you, you are before all things, and in you all things hold together.

We declare, Jesus, that you are the head of the body, the church. You are the beginning, the firstborn from the dead, in everything you are preeminent. For in you, Jesus, all the fullness of God was pleased to dwell and through you, to reconcile to yourself all things, whether on earth or in heaven, making peace by the blood of your cross.

We pray for Muslims in this area who are currently alienated and hostile in mind, doing evil deeds. We pray that they would accept and believe and be reconciled through Christ\'s body of flesh by His death, and that they would be presented to you as holy and blameless and above reproach before you.', 'ramadan-2026' ),
            ],
            //day 2
            [
                __( 'Day 2: Creation of Humans', 'ramadan-2026' ),
                __( 'Genesis 1:26-27
And God said, Let us make man in our image, after our likeness: and let them have dominion over the fish of the sea, and over the fowl of the air, and over the cattle, and over all the earth, and over every creeping thing that creepeth upon the earth. So God created man in his own image, in the image of God created he him; male and female created he them.

Genesis 2:15-25
And the LORD God took the man, and put him into the garden of Eden to dress it and to keep it. And the LORD God commanded the man, saying, Of every tree of the garden thou mayest freely eat: But of the tree of the knowledge of good and evil, thou shalt not eat of it: for in the day that thou eatest thereof thou shalt surely die. And the LORD God said, It is not good that the man should be alone; I will make him an help meet for him. And out of the ground the LORD God formed every beast of the field, and every fowl of the air; and brought them unto Adam to see what he would call them: and whatsoever Adam called every living creature, that was the name thereof. And Adam gave names to all cattle, and to the fowl of the air, and to every beast of the field; but for Adam there was not found an help meet for him. And the LORD God caused a deep sleep to fall upon Adam, and he slept: and he took one of his ribs, and closed up the flesh instead thereof; And the rib, which the LORD God had taken from man, made he a woman, and brought her unto the man. And Adam said, This is now bone of my bones, and flesh of my flesh: she shall be called Woman, because she was taken out of Man. Therefore shall a man leave his father and his mother, and shall cleave unto his wife: and they shall be one flesh. And they were both naked, the man and his wife, and were not ashamed.', 'ramadan-2026' ),
                __( 'A relational, purposeful Creator, God intentionally fashioned humanity through a deliberately ordered process, surrounding us with heavenly community and provision so we are never left alone.

- God made us in His image. Pray for the people of this land not only to recognize that they bear His image, but that it would also transform the way they view others.
- As they ponder the reality that their friends, family, neighbors, and enemies are all created in God\'s image, may they more rightly esteem them.', 'ramadan-2026' ),
                __( 'Made as image bearers from the dust of the earth and breathed upon, humanity is fundamentally communal. God created complementary male and female for companionship, multiplication, and stewardship of creation.

- Pray for those among this people who feel alone, misunderstood, and isolated. May the truth that he created us for relationship with him and others bring hope and stir a desire to understand more about how a personal relationship is possible with the God who created the heavens and earth.', 'ramadan-2026' ),
                __( 'This story shows God\'s original design for marriage and community–truths that can transform Muslim families.

Ahmed complained about his wife. She didn\'t clean the house the way she should. She didn\'t take care of her appearance the way she should. His teenage children stressed him out. He has had a girlfriend on the side for years who he traveled to spend the weekend with. Ultimately, because he didn\'t value God\'s design for marriage and whole life commitment, he divorced his wife and married the other woman.

- Pray for Muslims in this land who are in disappointing marriages. Pray for opportunities to read the story of Adam and Eve and understand God\'s original design for marriage.
- Pray that transformation would happen in their families as they trust God\'s word, and that he would bring beauty into these broken marriages for his glory.', 'ramadan-2026' ),
                __( 'This story emphasizes that a believer\'s place of belonging is in God\'s eternal family -- not cut off, separated, and in isolation.

- Pray for Christians working in this culture to clearly communicate this concept to all who are cut off from their earthly families because of their decision to follow Jesus.
- Pray that the truth that God puts the lonely in families would comfort them and remind them that they are not alone.', 'ramadan-2026' ),
                __( 'The story in Genesis 2:24 continues in Ephesians 5. Father, we pray for marriages among this people group to be transformed by the teachings of Jesus. We pray that men would love their wives the way that Christ loves the church, and we pray for wives to trust their husbands and follow them the way that the church should submit to Christ.

Ephesians 5:31-32 says, "Therefore a man shall leave his father and mother and hold fast to his wife, and the two shall become one flesh. This mystery is profound, and I am saying that it refers to Christ and the church."

Father, we bless this people group that they would experience oneness with Christ, and that this beautiful picture of marriage would point to the ultimate union: this people group united with Christ as one for your glory.', 'ramadan-2026' ),
            ],
            //day 3
            [
                __( 'Day 3: Humans Disobey God', 'ramadan-2026' ),
                __( 'Genesis 3:1-24
Now the serpent was more subtil than any beast of the field which the LORD God had made. And he said unto the woman, Yea, hath God said, Ye shall not eat of every tree of the garden? And the woman said unto the serpent, We may eat of the fruit of the trees of the garden: But of the fruit of the tree which is in the midst of the garden, God hath said, Ye shall not eat of it, neither shall ye touch it, lest ye die. And the serpent said unto the woman, Ye shall not surely die: For God doth know that in the day ye eat thereof, then your eyes shall be opened, and ye shall be as gods, knowing good and evil. And when the woman saw that the tree was good for food, and that it was pleasant to the eyes, and a tree to be desired to make one wise, she took of the fruit thereof, and did eat, and gave also unto her husband with her; and he did eat. And the eyes of them both were opened, and they knew that they were naked; and they sewed fig leaves together, and made themselves aprons. And they heard the voice of the LORD God walking in the garden in the cool of the day: and Adam and his wife hid themselves from the presence of the LORD God amongst the trees of the garden. And the LORD God called unto Adam, and said unto him, Where art thou? And he said, I heard thy voice in the garden, and I was afraid, because I was naked; and I hid myself. And he said, Who told thee that thou wast naked? Hast thou eaten of the tree, whereof I commanded thee that thou shouldest not eat? And the man said, The woman whom thou gavest to be with me, she gave me of the tree, and I did eat. And the LORD God said unto the woman, What is this that thou hast done? And the woman said, The serpent beguiled me, and I did eat. And the LORD God said unto the serpent, Because thou hast done this, thou art cursed above all cattle, and above every beast of the field; upon thy belly shalt thou go, and dust shalt thou eat all the days of thy life: And I will put enmity between thee and the woman, and between thy seed and her seed; it shall bruise thy head, and thou shalt bruise his heel. Unto the woman he said, I will greatly multiply thy sorrow and thy conception; in sorrow thou shalt bring forth children; and thy desire shall be to thy husband, and he shall rule over thee. And unto Adam he said, Because thou hast hearkened unto the voice of thy wife, and hast eaten of the tree, of which I commanded thee, saying, Thou shalt not eat of it: cursed is the ground for thy sake; in sorrow shalt thou eat of it all the days of thy life; Thorns also and thistles shall it bring forth to thee; and thou shalt eat the herb of the field; In the sweat of thy face shalt thou eat bread, till thou return unto the ground; for out of it wast thou taken: for dust thou art, and unto dust shalt thou return. And Adam called his wife\\\'s name Eve; because she was the mother of all living. Unto Adam also and to his wife did the LORD God make coats of skins, and clothed them. And the LORD God said, Behold, the man is become as one of us, to know good and evil: and now, lest he put forth his hand, and take also of the tree of life, and eat, and live for ever: Therefore the LORD God sent him forth from the garden of Eden, to till the ground from whence he was taken. So he drove out the man; and he placed at the east of the garden of Eden Cherubims, and a flaming sword which turned every way, to keep the way of the tree of life.', 'ramadan-2026' ),
                __( 'This passage shows God acting with justice, compassion, and mercy as He engages in dialogue with humanity following their disobedience. Despite this disobedience requiring discipline, He immediately launched a rescue plan to reverse the curse.

We see in Jesus\' day that many people did not understand the way of the Messiah. For example, though they had read Genesis 3:15 many times, "I will put enmity between you and the woman and between your offspring and her offspring. He shall bruise your head and you shall bruise his heel," they didn\'t see Jesus.

- In the same way, Muslims, when they read this passage, don\'t understand that Jesus is the fulfillment of this promise. Pray that by the Holy Spirit, their eyes would be opened to this reality.', 'ramadan-2026' ),
                __( 'This passage teaches us that disobedience to God\'s commands, even an act that seems very small and simple, like eating a forbidden fruit, results in eternal separation from him.

- As Muslims read this story, pray for God to reveal their separation from him. Whether they\'ve committed "big sins" or "small sins", may they recognize their guilt and continue to seek a solution through the reading of God\'s uncorrupted Word.', 'ramadan-2026' ),
                __( 'Jihene jumped in and interrupted Nour, the Christian telling the story of Genesis 3, "...and they ate the apple that God forbade them to eat and were immediately ashamed of their nakedness. As a result God kicked them out of heaven and sent them to earth."

Nour responded, "Yes, that\'s what the Quran teaches, but did you know that there are a lot more details to the story in the Taurat (Old Testament) than this summary?"

Jihene considers herself a good Muslim who has studied Islam, but who has never read the Taurat or Injeel. She is a \'good person with a clean heart\' and believes her good deeds will ensure a place in heaven. When she talks with Christians, she often shares her own unique version of Islam that borrows many ideas from Christianity and makes her feel good.

- Pray for many Muslims like Jihene to read this story and recognize the stain of sin that we all carry as people who have been disobedient to God.
- Ask God to stir curiosity to read and discover how God launched a rescue plan for all of us.', 'ramadan-2026' ),
                __( 'This story illustrates the guilt, shame, and fear that covered Adam and Eve when they sinned.

- Pray for Christians trying to reach Muslims in this area to prayerfully ponder their own testimony of entering God\'s Kingdom. Pray for them to recall who they were before Christ: ashamed, fearful, and guilty, and who they are now with Christ: forgiven, covered, and hopeful. Ask that they would be able to tell their salvation story in a way that points those they share with to want to read and understand the stories from the Bible.
- Pray for Christians to effectively describe how God has covered their shame and brought healing into their broken relationship with God in the same way that this story describes.', 'ramadan-2026' ),
                __( 'Father, we pray that the people of this land will rejoice in the truth of Romans 5:15-21, because the free gift is not like the trespass of Genesis 3. For if many died through Adam\'s trespass, how much more have the grace of God and the free gift by the grace of that one man Jesus Christ abounded for many?

We know that apart from Christ, this people is under judgment following that one trespass that brought condemnation, but the free gift following many trespasses brought justification.

For if because of one man\'s trespass death reigned through that one man, how much more will those who receive the abundance of grace and the free gift of righteousness reign in life through the one man, Jesus Christ?

May many receive the free gift of righteousness through Jesus Christ. Convict them of sin and may that conviction bring even greater joy and thankfulness for the free gift of righteousness that covers their sin.', 'ramadan-2026' ),
            ],
            //day 4
            [
                __( 'Day 4: God Destroys an Evil Humanity', 'ramadan-2026' ),
                __( 'Genesis 6:5-6
And GOD saw that the wickedness of man was great in the earth, and that every imagination of the thoughts of his heart was only evil continually. And it repented the LORD that he had made man on the earth, and it grieved him at his heart.

Genesis 6:9-22
These are the generations of Noah: Noah was a just man and perfect in his generations, and Noah walked with God. And Noah begat three sons, Shem, Ham, and Japheth. The earth also was corrupt before God, and the earth was filled with violence. And God looked upon the earth, and, behold, it was corrupt; for all flesh had corrupted his way upon the earth. And God said unto Noah, The end of all flesh is come before me; for the earth is filled with violence through them; and, behold, I will destroy them with the earth. Make thee an ark of gopher wood; rooms shalt thou make in the ark, and shalt pitch it within and without with pitch. And this is the fashion which thou shalt make it of: The length of the ark shall be three hundred cubits, the breadth of it fifty cubits, and the height of it thirty cubits. A window shalt thou make to the ark, and in a cubit shalt thou finish it above; and the door of the ark shalt thou set in the side thereof; with lower, second, and third stories shalt thou make it. And, behold, I, even I, do bring a flood of waters upon the earth, to destroy all flesh, wherein is the breath of life, from under heaven; and every thing that is in the earth shall die. But with thee will I establish my covenant; and thou shalt come into the ark, thou, and thy sons, and thy wife, and thy sons\\\' wives with thee. And of every living thing of all flesh, two of every sort shalt thou bring into the ark, to keep them alive with thee; they shall be male and female. Of fowls after their kind, and of cattle after their kind, of every creeping thing of the earth after his kind, two of every sort shall come unto thee, to keep them alive. And take thou unto thee of all food that is eaten, and thou shalt gather it to thee; and it shall be for food for thee, and for them. Thus did Noah; according to all that God commanded him, so did he.

Genesis 7:17-24
And the flood was forty days upon the earth; and the waters increased, and bare up the ark, and it was lift up above the earth. And the waters prevailed, and were increased greatly upon the earth; and the ark went upon the face of the waters. And the waters prevailed exceedingly upon the earth; and all the high hills, that were under the whole heaven, were covered. Fifteen cubits upward did the waters prevail; and the mountains were covered. And all flesh died that moved upon the earth, both of fowl, and of cattle, and of beast, and of every creeping thing that creepeth upon the earth, and every man: All in whose nostrils was the breath of life, of all that was in the dry land, died. And every living substance was destroyed which was upon the face of the ground, both man, and cattle, and the creeping things, and the fowl of the heaven; and they were destroyed from the earth: and Noah only remained alive, and they that were with him in the ark. And the waters prevailed upon the earth an hundred and fifty days.', 'ramadan-2026' ),
                __( 'In the midst of complete brokenness, God is committed to finding a way to restore relationship with His people.

- Pray for the people of this place to recognize the deep brokenness in their lives and to pursue the God who makes a way to restore relationships.', 'ramadan-2026' ),
                __( 'When man is left to his own devices, he is completely corrupt and will only do evil. There is no hope that we can please God in our own strength. Yet, in this passage, in spite of his imperfections, we see that Noah found favor in God\'s eyes.

- Pray for men and women in this place who genuinely want to please God to be given an opportunity to meet a Christian who will point them to read God\'s Word.
- Muslims claim to follow all the prophets (Adam, Abraham, Moses, Noah, David, Jesus, Mohammed) but most never read what any of the prophets (except for their prophet) actually said and did according to the Bible. They only have a few snippets about them in the Koran. Pray for understanding that if they claim to follow all the prophets, they need to know what all the prophets said and taught from the Old and New Testament.', 'ramadan-2026' ),
                __( '"You can\'t trust anyone. Everyone is out to take advantage of you. You need to be careful who you trust. I don\'t have any other person that I\'ve ever shared these deep hopes and dreams with," said Miriam. Miriam\'s words reflect a common struggle among Muslims.

In one sense, Muslims have a deep understanding of the fallen nature of mankind, evident in their lack of trust in people. On the other hand, they put all their hope in earning God\'s favor through their own works. The story of Noah makes it evident that there is none who are righteous and that we need a Savior who can protect us from the coming flood of consequences of our sin.

- Pray for Muslims in this place to realize that we are saved by grace, not by works.
- Pray with awareness that the revelation of these things to a Muslim\'s heart is a divine act of grace. Know that this is a spiritual battle and your prayers matter.', 'ramadan-2026' ),
                __( 'Isaiah 54:9-10 says, "\'This is like the days of Noah to me. As I swore that the waters of Noah should no more go over the earth, so I have sworn that I will not be angry with you and will not rebuke you. For the mountains may depart and the hills be removed, but my steadfast love shall not depart from you, and my covenant of peace shall not be removed,\' says the Lord who has compassion on you."

- Pray for the church in this place to be grounded in these realities. Pray for believers to know that God\'s steadfast love will not depart from them.
- May they interact with their Muslim neighbors and friends and co-workers with firm conviction that the covenant of peace they have with God shall not be removed. Pray that the compassion that God has given them overflow with compassion for the lost around them who do not have this truth.', 'ramadan-2026' ),
                __( 'Hebrews 11:7 says, "By faith Noah, being warned by God concerning events as yet unseen, in reverent fear, constructed an ark for the saving of his household. By this he condemned the world and became an heir of the righteousness that comes by faith."

Pray for Muslims in this place to be people like Noah who would have a reverent fear of God and who would take action toward the salvation of their household. Ask that they would be people who would gather their children, their siblings, aunts and uncles, and extended families to read God\'s Holy Word and consequently be transformed by it.

Pray for bold witnesses to emerge among this people who would proclaim the good news that they are heirs of righteousness that comes by faith.

Just as Noah is called a "herald of righteousness" in 2 Peter 2:5, may men and women be raised up as heralds of righteousness in every region for God\'s glory.', 'ramadan-2026' ),
            ],
            //day 5
            [
                __( 'Day 5: God\'s Covenant with Noah', 'ramadan-2026' ),
                __( 'Genesis 8:20-22
And Noah builded an altar unto the LORD; and took of every clean beast, and of every clean fowl, and offered burnt offerings on the altar. And the LORD smelled a sweet savour; and the LORD said in his heart, I will not again curse the ground any more for man\\\'s sake; for the imagination of man\\\'s heart is evil from his youth; neither will I again smite any more every thing living, as I have done. While the earth remaineth, seedtime and harvest, and cold and heat, and summer and winter, and day and night shall not cease.

Genesis 9:1-17
And God blessed Noah and his sons, and said unto them, Be fruitful, and multiply, and replenish the earth. And the fear of you and the dread of you shall be upon every beast of the earth, and upon every fowl of the air, upon all that moveth upon the earth, and upon all the fishes of the sea; into your hand are they delivered. Every moving thing that liveth shall be meat for you; even as the green herb have I given you all things. But flesh with the life thereof, which is the blood thereof, shall ye not eat. And surely your blood of your lives will I require; at the hand of every beast will I require it, and at the hand of man; at the hand of every man\\\'s brother will I require the life of man. Whoso sheddeth man\\\'s blood, by man shall his blood be shed: for in the image of God made he man. And you, be ye fruitful, and multiply; bring forth abundantly in the earth, and multiply therein. And God spake unto Noah, and to his sons with him, saying, And I, behold, I establish my covenant with you, and with your seed after you; And with every living creature that is with you, of the fowl, of the cattle, and of every beast of the earth with you; from all that go out of the ark, to every beast of the earth. And I will establish my covenant with you; neither shall all flesh be cut off any more by the waters of a flood; neither shall there any more be a flood to destroy the earth. And God said, This is the token of the covenant which I make between me and you and every living creature that is with you, for perpetual generations: I do set my bow in the cloud, and it shall be for a token of a covenant between me and the earth. And it shall come to pass, when I bring a cloud over the earth, that the bow shall be seen in the cloud: And I will remember my covenant, which is between me and you and every living creature of all flesh; and the waters shall no more become a flood to destroy all flesh. And the bow shall be in the cloud; and I will look upon it, that I may remember the everlasting covenant between God and every living creature of all flesh that is upon the earth. And God said unto Noah, This is the token of the covenant, which I have established between me and all flesh that is upon the earth.', 'ramadan-2026' ),
                __( 'Here we see God entering a covenant with man. Yet man has no responsibility; God takes it all. We also see that when God makes a promise, nothing could stop Him from keeping it.

- Pray for faith to grow in your heart and in the hearts of people serving this people group– that God\'s promise for all nations to be blessed includes this Muslim people group.
- Pray for increased expectation that God will fulfill His promise.', 'ramadan-2026' ),
                __( 'God desires us to fill the earth with His glory. Twice God commands Noah, "Be fruitful and multiply and fill the earth."

- Pray for God to raise up seekers among this people group whose curiosity would be fruitful and multiply, creating more seekers.
- Pray that God would raise up disciple makers among this people group whose passion would be fruitful and multiply, creating more disciple makers.
- Pray for God to raise up intercessors among this people group whose faith would be fruitful and multiply, creating more intercessors.
- Pray for God to raise up church planters among this people group whose sowing would be fruitful and multiply, creating more church planters.', 'ramadan-2026' ),
                __( 'There are a lot of similarities between the Quran\'s version of Noah\'s story and the Bible\'s. The Quran emphasizes Noah\'s preaching and moral lessons rather than narrative details.

- Pray that as Muslims read this story from the Bible their hearts would be drawn into the narrative and that they would understand that it\'s part of a greater narrative.
- Pray for a growing hunger to discover the larger Genesis to Revelation narrative and to learn their part in it.', 'ramadan-2026' ),
                __( 'Ibrahim came to Christ out of a Muslim background 20 years ago. For years, he led a house church and was involved in outreach that shared Christ with his people. This shifted in recent months, as he has been working with young believers in his city who are pursuing a simple, reproducible discipleship method (DMM). He jokes that he feels like he has been born again a second time, because of all of the beautiful fruit being borne as disciples are continuing to make disciples who in turn are making disciples.

- Pray that God would stir a hunger among every Christian, from this people group and outsiders trying to reach them, to be fruitful and multiply. Equip them with tools such as how to share their testimony, how to give a simple Gospel presentation, how to start discovery Bible studies, and prayer walking, so that they can go out into the harvest with confidence that God will lead them to genuine seekers.', 'ramadan-2026' ),
                __( 'The book of Acts describes the early church in Acts 6:7, "And the word of God continued to increase, and the number of the disciples multiplied greatly in Jerusalem, and a great many of the priests became obedient to the faith."

Acts 9:31 also says, "so the church throughout all Judea and Galilee and Samaria had peace and was being built up and walking in the fear of the Lord and in the comfort of the Holy Spirit, it multiplied."

Continuing in Acts 12:24, "but the word of God increased and multiplied."

We pray today that these same words would be spoken of this place. May the Word of God increase among this people group. May many become obedient to the faith here. May the church have peace as it is being built up. May believers among this people group walk in the fear of the Lord and in the comfort of the Holy Spirit. Ultimately may the number of disciples be multiplied greatly.', 'ramadan-2026' ),
            ],
            //day 6
            [
                __( 'Day 6: Tower of Babel', 'ramadan-2026' ),
                __( 'Genesis 11:1-9
And the whole earth was of one language, and of one speech. And it came to pass, as they journeyed from the east, that they found a plain in the land of Shinar; and they dwelt there. And they said one to another, Go to, let us make brick, and burn them throughly. And they had brick for stone, and slime had they for morter. And they said, Go to, let us build us a city and a tower, whose top may reach unto heaven; and let us make us a name, lest we be scattered abroad upon the face of the whole earth. And the LORD came down to see the city and the tower, which the children of men builded. And the LORD said, Behold, the people is one, and they have all one language; and this they begin to do: and now nothing will be restrained from them, which they have imagined to do. Go to, let us go down, and there confound their language, that they may not understand one another\\\'s speech. So the LORD scattered them abroad from thence upon the face of all the earth: and they left off to build the city. Therefore is the name of it called Babel; because the LORD did there confound the language of all the earth: and from thence did the LORD scatter them abroad upon the face of all the earth.', 'ramadan-2026' ),
                __( 'God is actively involved with the people He created. He is not far off. He observes and comes down to interact and intervene for humanity\'s good.

- Today as you pray for this place, thank God for His nearness.
- Pray for increasing numbers of people in this place to be aware of His presence.
- Ask God, according to His steadfast love, to intervene when people in this place reject Him and pursue their own desires.', 'ramadan-2026' ),
                __( 'We are prone to the desire to make a name for ourselves, and to go against God\'s command of filling the earth. We prefer comfort, security, and staying put.

This is illustrated in the story of Imen, a teacher who lived several hours away from her family. She saw many things in Christianity that appealed to her as she discussed Jesus with a Christian. Often she would say, "Wow, I really like that. That\'s really beautiful."

But when it came time for her to decide if she wanted to follow Jesus or just be a fan of Him, she felt fear of what the implications would be from her family and community. Ultimately, the comfort and security of staying where she was, like the builders on the \'plain in the land of Shinar\', won out.

- Pray for Muslims to be willing to follow and obey the truth despite the discomfort and unease accompanied by setting their hearts on a heavenly home.', 'ramadan-2026' ),
                __( 'Muslims would also condemn arrogance and defiance of God, like we see in the story of the Tower of Babel. However, most Muslims do not believe God would \'come down to see the city and the tower\'.

- Pray for Muslims in this place to have their vision of God increased. Pray for their understanding of His all-powerful qualities to grow in their hearts and minds and to comprehend that God would enter into His creation to save them.', 'ramadan-2026' ),
                __( 'Pray for Christians in this place -- whether Muslim background believers or foreigners who live among this people -- that they would reject the temptation to settle and just be comfortable. Pray that they would have courage to keep going: going into Muslim neighborhoods and homes, and making Muslim friends, all with the goal of sharing the love of God with Muslims and engaging them with His Word.

Pray for churches in this place to resist the temptation to build in such a way as to make a name for themselves, but that everything would be done for the glory of God.', 'ramadan-2026' ),
                __( 'In Acts 2, on the day of Pentecost God reverses the division caused in the story of the Tower of Babel. Instead of one language being divided into many languages, we see many languages being understood through one message, bringing unity.

This happened because the main actor in Acts 2 is the Spirit of God. Pray for the Spirit of God to be poured out on this land, resulting in multitudes believing in Jesus and churches being formed and multiplied. Pray that territorialism and division would end. Pray that any existing churches in this place would work together in unity for God\'s glory.', 'ramadan-2026' ),
            ],
            //day 7
            [
                __( 'Day 7: Abraham Trusted God', 'ramadan-2026' ),
                __( 'Genesis 12:1–7
Now the LORD had said unto Abram, Get thee out of thy country, and from thy kindred, and from thy father\\\'s house, unto a land that I will shew thee: And I will make of thee a great nation, and I will bless thee, and make thy name great; and thou shalt be a blessing: And I will bless them that bless thee, and curse him that curseth thee: and in thee shall all families of the earth be blessed. So Abram departed, as the LORD had spoken unto him; and Lot went with him: and Abram was seventy and five years old when he departed out of Haran. And Abram took Sarai his wife, and Lot his brother\\\'s son, and all their substance that they had gathered, and the souls that they had gotten in Haran; and they went forth to go into the land of Canaan; and into the land of Canaan they came. And Abram passed through the land unto the place of Sichem, unto the plain of Moreh. And the Canaanite was then in the land. And the LORD appeared unto Abram, and said, Unto thy seed will I give this land: and there builded he an altar unto the LORD, who appeared unto him.

Genesis 15:1–6
After these things the word of the LORD came unto Abram in a vision, saying, Fear not, Abram: I am thy shield, and thy exceeding great reward. And Abram said, Lord GOD, what wilt thou give me, seeing I go childless, and the steward of my house is this Eliezer of Damascus? And Abram said, Behold, to me thou hast given no seed: and, lo, one born in my house is mine heir. And, behold, the word of the LORD came unto him, saying, This shall not be thine heir; but he that shall come forth out of thine own bowels shall be thine heir. And he brought him forth abroad, and said, Look now toward heaven, and tell the stars, if thou be able to number them: and he said unto him, So shall thy seed be. And he believed in the LORD; and he counted it to him for righteousness.', 'ramadan-2026' ),
                __( 'God appears to people and speaks through visions. We also learn that He is trustworthy and obedience to Him is worth giving up anything -- including family, land, and country.

- Pray for people in this place to have visions of God speaking to them and calling them to follow Him, even if it is scary or unnerving for them.
- As Abraham observed the stars and believed the Lord\'s promise to him, ask God to reveal His power, goodness, and promises to people of this land through creation. Pray that He would woo them to Himself and stir a curiosity in them to seek out the Bible.', 'ramadan-2026' ),
                __( 'We are not counted as righteous based on our deeds, but by our trust and belief in God. We also learn that God chooses to work through humans to bless others and to reach them with His good news.

- Pray for God to raise up people among this people group like Abraham who would obey Him and share the good news of Jesus with family members, friends, neighbors, and co-workers.
- Pray for Muslims to repent of thinking their good deeds will cover their sin and earn them a place in heaven. Pray for them to understand that righteousness comes through faith alone.', 'ramadan-2026' ),
                __( 'Narimen had been married for eight years and was unable to have children. Her sister-in-law had three already. When she found out she was pregnant, she was ready to abort because of the cost of raising another child. Narimen pleaded with her to keep the baby, telling her that she would raise the child. The sister-in-law agreed and, as Narimen began to buy things for the baby, her love and hopes for this child grew. When it came time for her sister-in-law to go into labor, she didn\'t tell Narimen. Ultimately she changed her mind and kept the baby. Narimen was crushed.

Like Sarah who waited for the fulfillment of God\'s promise, throughout the Muslim world, women carry the shame of infertility.

- Pray that Muslims who read the story of Abraham and Sarah would find hope in a God who promises descendants, even if they are spiritual offspring.
- Pray for hope to grow in their hearts as the covenant-keeping God reveals Himself to them.', 'ramadan-2026' ),
                __( 'Pray for Christians in this area who are trying to reach Muslims with the good news of Jesus.

- Pray that they would continually be able to testify that God is their shield and their reward. Pray that they would experience His protection, His comfort, and His provision in rich ways.
- Pray for a desire to grow among all Christians in this region to see generations of believers flowing out of them as streams of spiritual children because of their faithful trust in God.
- Pray for confidence and faith to grow among Christians in this area– faith that Muslim families are included in the promise that all the families of the earth shall be blessed through Abraham. May they have eyes of faith for the Muslim families all around them -- even extremists and strong followers of Islam.', 'ramadan-2026' ),
                __( 'Father, just as Romans 4:17 says that Abraham believed God, "the one who gives life to the dead and calls into existence the things that do not exist", give us eyes of faith to believe for multiplying disciples and churches among this people group.

Raise up believers in this area who, like Abraham, would in hope believe against hope and become the spiritual fathers and mothers of a nation, leading multiplying generations to faith in Christ.

We pray that no unbelief would make men and women in this area waver concerning the promise of God, but that they would grow strong in faith as they give glory to God. May they be fully convinced that God is able to do what He has promised.', 'ramadan-2026' ),
            ],
            //day 8
            [
                __( 'Day 8: Abraham Obeyed God', 'ramadan-2026' ),
                __( 'Genesis 22:1–19
And it came to pass after these things, that God did tempt Abraham, and said unto him, Abraham: and he said, Behold, here I am. And he said, Take now thy son, thine only son Isaac, whom thou lovest, and get thee into the land of Moriah; and offer him there for a burnt offering upon one of the mountains which I will tell thee of. And Abraham rose up early in the morning, and saddled his ass, and took two of his young men with him, and Isaac his son, and clave the wood for the burnt offering, and rose up, and went unto the place of which God had told him. Then on the third day Abraham lifted up his eyes, and saw the place afar off. And Abraham said unto his young men, Abide ye here with the ass; and I and the lad will go yonder and worship, and come again to you. And Abraham took the wood of the burnt offering, and laid it upon Isaac his son; and he took the fire in his hand, and a knife; and they went both of them together. And Isaac spake unto Abraham his father, and said, My father: and he said, Here am I, my son. And he said, Behold the fire and the wood: but where is the lamb for a burnt offering? And Abraham said, My son, God will provide himself a lamb for a burnt offering: so they went both of them together. And they came to the place which God had told him of; and Abraham built an altar there, and laid the wood in order, and bound Isaac his son, and laid him on the altar upon the wood. And Abraham stretched forth his hand, and took the knife to slay his son. And the angel of the LORD called unto him out of heaven, and said, Abraham, Abraham: and he said, Here am I. And he said, Lay not thine hand upon the lad, neither do thou any thing unto him: for now I know that thou fearest God, seeing thou hast not withheld thy son, thine only son from me. And Abraham lifted up his eyes, and looked, and behold behind him a ram caught in a thicket by his horns: and Abraham went and took the ram, and offered him up for a burnt offering in the stead of his son. And Abraham called the name of that place Jehovah-jireh: as it is said to this day, In the mount of the LORD it shall be seen. And the angel of the LORD called unto Abraham out of heaven the second time, And said, By myself have I sworn, saith the LORD, for because thou hast done this thing, and hast not withheld thy son, thine only son: That in blessing I will bless thee, and in multiplying I will multiply thy seed as the stars of the heaven, and as the sand which is upon the sea shore; and thy seed shall possess the gate of his enemies; And in thy seed shall all the nations of the earth be blessed; because thou hast obeyed my voice. So Abraham returned unto his young men, and they rose up and went together to Beer-sheba; and Abraham dwelt at Beer-sheba.', 'ramadan-2026' ),
                __( 'This passage teaches us that God will ask us to do things that do not make sense to us. He desires obedience to His commands, and He is never contrary to His nature.

Historians agree that child sacrifice was widely practiced during Abraham\'s day. So, Abraham would have been familiar with a command to sacrifice his child. What was surprising was that God Himself intervened, provided a substitute, showing that He could be completely trusted.

- Pray for Muslims, when they read this story, to have their trust in God grow.
- Pray for Christians trying to reach Muslims in this place to rejoice, though now for a little while they are grieved by various trials, so that the tested genuineness of their faith, more precious than gold, that perishes though it is refined by fire, may be found to result in praise and glory and honor at the revelation of Jesus Christ (1 Peter 1:6-7).', 'ramadan-2026' ),
                __( 'This passage teaches us that humans can hear God\'s voice. We learn that man can be tested by God but that it is ultimately for our good.

- Pray for Muslims in this land to hear God\'s voice, whether through dreams, visions, or the reading of the Bible.
- Pray for them to grow in hearing His voice and to respond in obedience to what they\'ve understood of what He said.', 'ramadan-2026' ),
                __( 'Seventy days after Ramadan ends, Muslims gather together in homes to celebrate Eid al-Adha and slaughter a sheep to commemorate this story, though they do not understand the fullness of it. Muslims reject the idea of a substitutionary sacrifice as we understand it in Christ. Yet every year they celebrate this story of a substitutionary sacrifice.

- Ask God to open hearts and open minds. Help them see how he provided a ram to stop Abraham from sacrificing his son to foreshadow his plan to send his only Son hundreds of years later as a substitutionary sacrifice on our behalf.', 'ramadan-2026' ),
                __( 'This story shows us Abraham\'s radical, immediate, and costly obedience. When God told him to take his son, whom he loves, and to offer him as a burnt offering, the very next morning Abraham rose early and began that journey.

- Pray for believers in this place to also exhibit radical, immediate, costly obedience to a good God who loves them and loves the Muslim people surrounding them.', 'ramadan-2026' ),
                __( 'Hebrews 11:17 says, "By faith Abraham, when he was tested, offered up Isaac, and he who had received the promises was in the act of offering up his only son, of whom it was said, \'Through Isaac shall your offspring be named.\' He considered that God was able even to raise him from the dead, from which, figuratively speaking, he did receive him back."

Lord, grow such a strong confidence and trust in You among believers in this place that they would see miracles happen and His name glorified as many Muslims turn to faith in Jesus.

Unlike the temporary sacrifices of the Old Covenant, we know that it is impossible for the blood of bulls and goats to take away sins. Pray for Muslims in this place to trust in Christ, who when He offered for all time a single sacrifice for sin, sat down at the right hand of God. May they believe by His single offering, He has perfected for all time those who are being sanctified.', 'ramadan-2026' ),
            ],
            //day 9
            [
                __( 'Day 9: God\'s Call to Moses', 'ramadan-2026' ),
                __( 'Exodus 2:23-25
And it came to pass in process of time, that the king of Egypt died: and the children of Israel sighed by reason of the bondage, and they cried, and their cry came up unto God by reason of the bondage. And God heard their groaning, and God remembered his covenant with Abraham, with Isaac, and with Jacob. And God looked upon the children of Israel, and God had respect unto them.

Exodus 3:1-14
Now Moses kept the flock of Jethro his father in law, the priest of Midian: and he led the flock to the backside of the desert, and came to the mountain of God, even to Horeb. And the angel of the LORD appeared unto him in a flame of fire out of the midst of a bush: and he looked, and, behold, the bush burned with fire, and the bush was not consumed. And Moses said, I will now turn aside, and see this great sight, why the bush is not burnt. And when the LORD saw that he turned aside to see, God called unto him out of the midst of the bush, and said, Moses, Moses. And he said, Here am I. And he said, Draw not nigh hither: put off thy shoes from off thy feet, for the place whereon thou standest is holy ground. Moreover he said, I am the God of thy father, the God of Abraham, the God of Isaac, and the God of Jacob. And Moses hid his face; for he was afraid to look upon God. And the LORD said, I have surely seen the affliction of my people which are in Egypt, and have heard their cry by reason of their taskmasters; for I know their sorrows; And I am come down to deliver them out of the hand of the Egyptians, and to bring them up out of that land unto a good land and a large, unto a land flowing with milk and honey; unto the place of the Canaanites, and the Hittites, and the Amorites, and the Perizzites, and the Hivites, and the Jebusites. Now therefore, behold, the cry of the children of Israel is come unto me: and I have also seen the oppression wherewith the Egyptians oppress them. Come now therefore, and I will send thee unto Pharaoh, that thou mayest bring forth my people the children of Israel out of Egypt. And Moses said unto God, Who am I, that I should go unto Pharaoh, and that I should bring forth the children of Israel out of Egypt? And he said, Certainly I will be with thee; and this shall be a token unto thee, that I have sent thee: When thou hast brought forth the people out of Egypt, ye shall serve God upon this mountain. And Moses said unto God, Behold, when I come unto the children of Israel, and shall say unto them, The God of your fathers hath sent me unto you; and they shall say to me, What is his name? what shall I say unto them? And God said unto Moses, I AM THAT I AM: and he said, Thus shalt thou say unto the children of Israel, I AM hath sent me unto you.

Exodus 7:1-5
And the LORD said unto Moses, See, I have made thee a god to Pharaoh: and Aaron thy brother shall be thy prophet. Thou shalt speak all that I command thee: and Aaron thy brother shall speak unto Pharaoh, that he send the children of Israel out of his land. And I will harden Pharaoh\\\'s heart, and multiply my signs and my wonders in the land of Egypt. But Pharaoh shall not hearken unto you, that I may lay my hand upon Egypt, and bring forth mine armies, and my people the children of Israel, out of the land of Egypt by great judgments. And the Egyptians shall know that I am the LORD, when I stretch forth mine hand upon Egypt, and bring out the children of Israel from among them.', 'ramadan-2026' ),
                __( 'God hears, He sees suffering, and He comes to end it at just the right time.

- Pray for those who are oppressed in this land to know that their sufferings are seen by God. Pray that they would have hope that He would intervene and for those circumstances to be used to ultimately bring them into a saving relationship with Him.
- Pray for oppressors in this land to repent. Pray they would not be like Pharaoh whose heart was hard, but that they would have Saul-to-Paul like conversion experiences.', 'ramadan-2026' ),
                __( 'Moses\' obedience to God\'s calling impacted an entire nation.

- Pray that God would raise up men and women in this land who would faithfully listen to the voice of God, and that it would bring about freedom from sin for multitudes in their nation.

We also learn that God delights to use people who feel inadequate and are humble.

- Pray that those who humble themselves in this land today, would be exalted and that God would humble those who exalt themselves over others.', 'ramadan-2026' ),
                __( 'This story shows us that the proof that God spoke through Moses was when the things that Moses spoke actually came to happen. Pray for Muslims to ponder the meaning of the word \'prophet\'. They claim to believe all of the prophets (including Abraham, Moses, Jesus) though most have never read their actual teachings in the Old Testament and New Testament. Pray for God to use believers to challenge this reality and for a curiosity to grow among Muslims all over this land to want to read what all of the prophets taught.

This hunger to know what the prophets truly taught can lead to life-changing encounters. Souad was a divorced woman who feared God and tried to practice Islam correctly. She raised her children to follow Islam. After they were grown and out of the house she bought several expensive Islamic books to do deep research. The further she went, the more doubt began to grow in her heart. She became curious about Jesus. Late one night, she fell on her knees and prayed. The room was filled with light and a voice spoke clearly, "I am He." These were the same words in Arabic that she would later read in the New Testament- Jesus\' declaration of His divine identity. After she understood what the prophets taught about Him, she would choose to make Him her Lord and Savior.', 'ramadan-2026' ),
                __( 'Christians living among Muslim peoples, especially those who have chosen to follow Christ out of a Muslim background, often live in fear of persecution because of their faith.

- Pray that God\'s promise of rescue, freedom, and hope in this story would thrill their hearts and stir them to act with boldness and agreement with all of His commands.
- Pray for Christians trying to serve Muslims by sharing the Good News of Jesus, despite the inadequacies they feel, to have a heart posture that says, "Here I am."', 'ramadan-2026' ),
                __( 'Two thousand years later, John 8:56-59 records Jesus saying to a group of religious leaders, "\'Your father Abraham rejoiced that he would see my day. He saw it and was glad.\'"

One religious leader said to Him, "You\'re not yet 50 years old, and have you seen Abraham?" Jesus said to them, "Truly, truly, I say to you, before Abraham was, I am."

This statement so outraged the religious leaders that they picked up stones to stone Him. He used the same title God used in this passage from Exodus 3 to declare who He is. Pray for Muslims in this land to have their eyes opened to the reality that Jesus is greater than Abraham, Moses, and all the prophets and that He came to set His people free – not just from Pharaoh, but from sin.', 'ramadan-2026' ),
            ],
            //day 10
            [
                __( 'Day 10: The Passover Sacrifice', 'ramadan-2026' ),
                __( 'Exodus 12:1-3
And the LORD spake unto Moses and Aaron in the land of Egypt, saying, This month shall be unto you the beginning of months: it shall be the first month of the year to you. Speak ye unto all the congregation of Israel, saying, In the tenth day of this month they shall take to them every man a lamb, according to the house of their fathers, a lamb for an house:

Exodus 12:21-31
Then Moses called for all the elders of Israel, and said unto them, Draw out and take you a lamb according to your families, and kill the passover. And ye shall take a bunch of hyssop, and dip it in the blood that is in the bason, and strike the lintel and the two side posts with the blood that is in the bason; and none of you shall go out at the door of his house until the morning. For the LORD will pass through to smite the Egyptians; and when he seeth the blood upon the lintel, and on the two side posts, the LORD will pass over the door, and will not suffer the destroyer to come in unto your houses to smite you. And ye shall observe this thing for an ordinance to thee and to thy sons for ever. And it shall come to pass, when ye be come to the land which the LORD will give you, according as he hath promised, that ye shall keep this service. And it shall come to pass, when your children shall say unto you, What mean ye by this service? That ye shall say, It is the sacrifice of the LORD\\\'s passover, who passed over the houses of the children of Israel in Egypt, when he smote the Egyptians, and delivered our houses. And the people bowed the head and worshipped. And the children of Israel went away, and did as the LORD had commanded Moses and Aaron, so did they. And it came to pass, that at midnight the LORD smote all the firstborn in the land of Egypt, from the firstborn of Pharaoh that sat on his throne unto the firstborn of the captive that was in the dungeon; and all the firstborn of cattle. And Pharaoh rose up in the night, he, and all his servants, and all the Egyptians; and there was a great cry in Egypt; for there was not a house where there was not one dead. And he called for Moses and Aaron by night, and said, Rise up, and get you forth from among my people, both ye and the children of Israel; and go, serve the LORD, as ye have said.

Exodus 12:40–42
Now the sojourning of the children of Israel, who dwelt in Egypt, was four hundred and thirty years. And it came to pass at the end of the four hundred and thirty years, even the selfsame day it came to pass, that all the hosts of the LORD went out from the land of Egypt. It is a night to be much observed unto the LORD for bringing them out from the land of Egypt: this is that night of the LORD to be observed of all the children of Israel in their generations.', 'ramadan-2026' ),
                __( 'God makes a way for people to escape His righteous judgment -- through the blood of a spotless lamb.

- Pray for Muslims in this place to feel the weight of their sin.
- Pray that their acceptance of God\'s great mercy would not lead them to passivity, but would drive them to want to know the way that God\'s mercy could cover over their sins.', 'ramadan-2026' ),
                __( 'Man\'s efforts to oppose God\'s plans to set His people free are futile.

- Pray for Muslims in this place to have a greater fear of God than a fear of what man could do to them for seeking the truth.

We also learn that God desires us to pass on to our children the stories of the works He has done in our lives from generation to generation.

- Pray for seekers and those on a journey among this people group to not only seek God themselves, but to desire to pass on what they have learned to future generations, both biological and spiritual.', 'ramadan-2026' ),
                __( 'Sitting, enjoying the fun of a wedding, Samar left to answer a phone call from her husband. He told her that their son\'s best friend had died in an accident. When Samar went to tell her cousin that she needed to leave because there had been a death of a son, the cousin panicked, initially thinking it was her firstborn son who had died. As the women tried to make sense of what had happened they tried to comfort themselves by saying "maktoub" -- it was simply \'written\' or predestined by God that this young man would die.

- Pray for the good news of a God who did not spare His firstborn and only Son so that families all over the world would never experience eternal death and separation from loved ones to penetrate this land.
- Pray that the crippling fear many Muslims walk with, because of their lack of assurance of God\'s mercy for them and their loved ones, would drive them to want to understand the unshakable hope followers of Christ carry even in the midst of extreme suffering.', 'ramadan-2026' ),
                __( '- Pray that just as this story shows the nation of Israel entering into God\'s redemptive story, believers trying to reach Muslims in this land would agree with their role in God\'s redemptive story for this place.
- Pray for Christian families to regularly celebrate the freedom they\'ve experienced from sin by being covered by the blood of the precious Lamb of God, and that testimony would be wisely and boldly proclaimed to their neighbors, coworkers, and friends.', 'ramadan-2026' ),
                __( 'Lord, we pray that there would be many in this land who would call on you as Father, who judges impartially according to each one\'s deeds. May they conduct themselves with fear throughout the time of their exile, knowing that they were ransomed from the futile ways inherited from their forefathers, not with perishable things such as silver or gold, but with the precious blood of Christ, like that of a lamb without blemish or spot. (1 Peter 1:17-19)

In Luke 22:14-20 Jesus said: "\'I have earnestly desired to eat this Passover with you before I suffer. For I tell you, I will not eat it until it is fulfilled in the kingdom of God.\' And he took a cup and when he had given thanks, he said, \'Take this and divide it among yourselves. For I tell you that from now on, I will not drink of the fruit of the vine until the kingdom of God comes.\' And he took bread when he had given thanks, he broke it and gave it to them saying, \'This is my body, which is given for you. Do this in remembrance of me.\' And likewise the cup after they had eaten, saying, \'This cup that is poured out for you is the new covenant in my blood.\'" Pray that multitudes of families in this place would take part in the new covenant that Jesus invited them into through His death, burial, and resurrection.', 'ramadan-2026' ),
            ],
            //day 11
            [
                __( 'Day 11: The Ten Commandments', 'ramadan-2026' ),
                __( 'Exodus 20:1–17
And God spake all these words, saying, I am the LORD thy God, which have brought thee out of the land of Egypt, out of the house of bondage. Thou shalt have no other gods before me. Thou shalt not make unto thee any graven image, or any likeness of any thing that is in heaven above, or that is in the earth beneath, or that is in the water under the earth: Thou shalt not bow down thyself to them, nor serve them: for I the LORD thy God am a jealous God, visiting the iniquity of the fathers upon the children unto the third and fourth generation of them that hate me; And shewing mercy unto thousands of them that love me, and keep my commandments. Thou shalt not take the name of the LORD thy God in vain; for the LORD will not hold him guiltless that taketh his name in vain. Remember the sabbath day, to keep it holy. Six days shalt thou labour, and do all thy work: But the seventh day is the sabbath of the LORD thy God: in it thou shalt not do any work, thou, nor thy son, nor thy daughter, thy manservant, nor thy maidservant, nor thy cattle, nor thy stranger that is within thy gates: For in six days the LORD made heaven and earth, the sea, and all that in them is, and rested the seventh day: wherefore the LORD blessed the sabbath day, and hallowed it. Honour thy father and thy mother: that thy days may be long upon the land which the LORD thy God giveth thee. Thou shalt not kill. Thou shalt not commit adultery. Thou shalt not steal. Thou shalt not bear false witness against thy neighbour. Thou shalt not covet thy neighbour\\\'s house, thou shalt not covet thy neighbour\\\'s wife, nor his manservant, nor his maidservant, nor his ox, nor his ass, nor any thing that is thy neighbour\\\'s.

Leviticus 6:1-7
And the LORD spake unto Moses, saying, If a soul sin, and commit a trespass against the LORD, and lie unto his neighbour in that which was delivered him to keep, or in fellowship, or in a thing taken away by violence, or hath deceived his neighbour; Or have found that which was lost, and lieth concerning it, and sweareth falsely; in any of all these that a man doeth, sinning therein: Then it shall be, because he hath sinned, and is guilty, that he shall restore that which he took violently away, or the thing which he hath deceitfully gotten, or that which was delivered him to keep, or the lost thing which he found, Or all that about which he hath sworn falsely; he shall even restore it in the principal, and shall add the fifth part more thereto, and give it unto him to whom it appertaineth, in the day of his trespass offering. And he shall bring his trespass offering unto the LORD, a ram without blemish out of the flock, with thy estimation, for a trespass offering, unto the priest: And the priest shall make an atonement for him before the LORD: and it shall be forgiven him for any thing of all that he hath done in trespassing therein.', 'ramadan-2026' ),
                __( 'God desires a relationship with His people that will reflect Him to the rest of the world. As Creator, He institutes laws to help them live flourishing lives as citizens in His Kingdom.

- Pray for people in this land to view God\'s commandments not as infringements upon their personal freedom, but as loving guardrails to protect them.

This passage also teaches us that God views our evil acts against others as a breach of faith against the Lord.

- Pray for Muslims here to recognize the image of God in the people around them and, out of obedience to God, to seek their good and not to take advantage of them.
- Pray for recognition of all the times they have infringed on the rights of others. May it convict them of their need of God\'s forgiveness.', 'ramadan-2026' ),
                __( 'This passage teaches us that humans are incredibly prone to worship things that are not the one true God, the Creator of the heavens and the earth.

- Pray for God\'s grace to bring Muslims in this land to repent of all the ways they have treasured idols like money, comfort, esteem of man, self-righteousness, and others.
- Pray for this conviction to drive them to the Good News of Jesus.', 'ramadan-2026' ),
                __( 'Amira is a Muslim from a neighboring country. She faces exploitation and being taken advantage of often when she leaves her home. "A taxi driver tried to charge me four times the correct price and when I pushed back, he said, \'You\'re a rich Muslim from another country, you have oil, you can afford it.\' I hate the people of this land and their underhanded ways." Amira\'s experience reflects the brokenness and dishonesty that pervades when people ignore God\'s commands.

Pray for the dishonest financial deals, fraud, theft and lying that plague humanity to reveal the true state of human nature and to drive Muslims in this land to seek a real solution -- the new heart of flesh that God promised in Ezekiel 36:26 to give to His people through Christ.', 'ramadan-2026' ),
                __( 'The command not to take the name of the Lord in vain includes more than just avoiding swearing; it also means not bearing God\'s name in an unworthy way. Pray that Christians here would be known for living holy and respectful lives—lives that reflect a genuine fear of God, love for all people, and bring honor to the name of Jesus.', 'ramadan-2026' ),
                __( 'Paul explains in Romans 7:7, "Yet if it had not been for the law, I would not have known sin. For I would not have known what it is to covet if the law had not said, \'You shall not covet.\'" He continues in verses 24 and 25, "Wretched man that I am, who will deliver me from this body of death? Thanks be to God through Jesus Christ our Lord!"

Then in Romans 8:1-2, "There is therefore now no condemnation for those who are in Christ Jesus. For the law of the Spirit of life has set you free in Christ Jesus from the law of sin and death."

Pray that Muslims would understand that God\'s moral standards and righteous requirements reveal our inability to fully obey on our own, but that Jesus came to set us free from the law of sin and death. Pray that they would experience the same revelation Paul described in Romans 7 and 8.', 'ramadan-2026' ),
            ],
            //day 12
            [
                __( 'Day 12: Cycles of Disobedience', 'ramadan-2026' ),
                __( 'Judges 2:10-23
And also all that generation were gathered unto their fathers: and there arose another generation after them, which knew not the LORD, nor yet the works which he had done for Israel. And the children of Israel did evil in the sight of the LORD, and served Baalim: And they forsook the LORD God of their fathers, which brought them out of the land of Egypt, and followed other gods, of the gods of the people that were round about them, and bowed themselves unto them, and provoked the LORD to anger. And they forsook the LORD, and served Baal and Ashtaroth. And the anger of the LORD was hot against Israel, and he delivered them into the hands of spoilers that spoiled them, and he sold them into the hands of their enemies round about, so that they could not any longer stand before their enemies. Whithersoever they went out, the hand of the LORD was against them for evil, as the LORD had said, and as the LORD had sworn unto them: and they were greatly distressed. Nevertheless the LORD raised up judges, which delivered them out of the hand of those that spoiled them. And yet they would not hearken unto their judges, but they went a whoring after other gods, and bowed themselves unto them: they turned quickly out of the way which their fathers walked in, obeying the commandments of the LORD; but they did not so. And when the LORD raised them up judges, then the LORD was with the judge, and delivered them out of the hand of their enemies all the days of the judge: for it repented the LORD because of their groanings by reason of them that oppressed them and vexed them. And it came to pass, when the judge was dead, that they returned, and corrupted themselves more than their fathers, in following other gods to serve them, and to bow down unto them; they ceased not from their own doings, nor from their stubborn way. And the anger of the LORD was hot against Israel; and he said, Because that this people hath transgressed my covenant which I commanded their fathers, and have not hearkened unto my voice; I also will not henceforth drive out any from before them of the nations which Joshua left when he died: That through them I may prove Israel, whether they will keep the way of the LORD to walk therein, as their fathers did keep it, or not. Therefore the LORD left those nations, without driving them out hastily; neither delivered he them into the hand of Joshua.', 'ramadan-2026' ),
                __( 'God is both personal and emotional. In this passage, we see both His anger and compassion motivating Him to act on behalf of His people. We also see that God allows people to choose whom they will love and serve. When they reject Him and follow other gods, he releases them to the consequences of their decisions.

- Pray for God\'s true nature to be made known in this place -- His holiness and His love, His compassion and His justice -- and for people to understand how these characteristics are perfectly consistent with one another.
- Pray for repentance from reshaping God into a more comfortable image, and for hearts to allow Him to speak directly through His Word.
- Pray that when Muslims read the Bible, God\'s Word and His character would be clearly understood.', 'ramadan-2026' ),
                __( 'This passage teaches us that humanity is quick to forget the works of God in their lives and are easily influenced by the beliefs of those around them. Instead of remembering God\'s faithfulness, people often drift toward what is popular, familiar, or culturally accepted.

- Pray for God to raise up countercultural people who would resist the ease of following the tide of culture in order to pursue truth and righteousness.
- Pray for people in this place to repent of idols such as comfort, security, money, relationships, and the approval of others, and to choose instead the covenant-keeping God who delivers them from the bondage to these lesser gods.', 'ramadan-2026' ),
                __( 'Picking olives far out in the country one crisp December morning, Naima, a single woman home on vacation, eagerly shared her thoughts with her family and the visitor in their midst. She talked about angels, evil spirits, the healing power of the Qur\'an, and her convictions about true religion. When one of the other olive pickers shared about her faith in the Messiah, Naima immediately responded, "You Christians worship three gods." "No," the believer replied, "we worship one God." "What about the Trinity?" Naima accused. The visitor replied, "If you want to understand that you need to take the time to actually read God\'s Holy Word and understand the plan He has given to return us back to him in relationship." To this Naima dismissively responded, "The Bible is corrupted. The Jews changed God\'s word because they didn\'t like it and that\'s why God sent the Qur\'an."

This memorized argument is repeated all around the world, often without understanding its validity or understanding that God\'s Word does not change or fail. True prophets call people back to the truth of His Word, they do not invent new truth.

- Pray for Muslims to grow dissatisfied with rehearsed answers and to courageously engage in critical thinking about what they have been taught.
- Pray that this journey would lead them to encounter truth in God\'s Word.', 'ramadan-2026' ),
                __( 'This passage reminds us that God does not have grandchildren. He only has children. Faith must be personally owned, not merely inherited.

- Pray for believers in this land to authentically live out and pass on their faith to the next generation.
- Pray for them to faithfully share their testimony of God\'s redeeming work in their lives. Pray that they would continually remind themselves and their children of God\'s living and active power.
- Ask that children of believers would encounter God personally and develop their own stories of the work of God in their lives that will root them deeply in Jesus.', 'ramadan-2026' ),
                __( 'In Acts 13, Paul and his companions arrive in Antioch in Pisidia. On the Sabbath, they went into the synagogue and sat down. They began retelling Israel\'s story, including the story found in our previous passage in Judges.

"For forty years he put up with them in the wilderness, and after destroying seven nations in the land of Canaan, he gave them their land as an inheritance. All this took about 450 years, and after that he gave them judges until Samuel the prophet..." (Acts 13:18-20) They continued the story through to Christ, declaring: "Let it be known to you, therefore, brothers, that through this man, Jesus, forgiveness of sins is proclaimed to you, and by him everyone who believes is freed from everything from which you could not be freed by the law of Moses."

Their message received mixed responses. Paul and Barnabas were opposed by some, yet Acts 13:48-49 tells us: "as many as were appointed to eternal life believed. And the word of the Lord was spreading throughout the whole region." Pray for believers to be empowered to preach boldly whether they are reviled or whether the Word of the Lord spreads rapidly, trusting God with the outcome.', 'ramadan-2026' ),
            ],
            //day 13
            [
                __( 'Day 13: The Suffering Servant of Christ', 'ramadan-2026' ),
                __( 'Isaiah 52:13-53:12
Behold, my servant shall deal prudently, he shall be exalted and extolled, and be very high. As many were astonied at thee; his visage was so marred more than any man, and his form more than the sons of men: So shall he sprinkle many nations; the kings shall shut their mouths at him: for that which had not been told them shall they see; and that which they had not heard shall they consider. Who hath believed our report? and to whom is the arm of the LORD revealed? For he shall grow up before him as a tender plant, and as a root out of a dry ground: he hath no form nor comeliness; and when we shall see him, there is no beauty that we should desire him. He is despised and rejected of men; a man of sorrows, and acquainted with grief: and we hid as it were our faces from him; he was despised, and we esteemed him not. Surely he hath borne our griefs, and carried our sorrows: yet we did esteem him stricken, smitten of God, and afflicted. But he was wounded for our transgressions, he was bruised for our iniquities: the chastisement of our peace was upon him; and with his stripes we are healed. All we like sheep have gone astray; we have turned every one to his own way; and the LORD hath laid on him the iniquity of us all. He was oppressed, and he was afflicted, yet he opened not his mouth: he is brought as a lamb to the slaughter, and as a sheep before her shearers is dumb, so he openeth not his mouth. He was taken from prison and from judgment: and who shall declare his generation? for he was cut off out of the land of the living: for the transgression of my people was he stricken. And he made his grave with the wicked, and with the rich in his death; because he had done no violence, neither was any deceit in his mouth. Yet it pleased the LORD to bruise him; he hath put him to grief: when thou shalt make his soul an offering for sin, he shall see his seed, he shall prolong his days, and the pleasure of the LORD shall prosper in his hand. He shall see of the travail of his soul, and shall be satisfied: by his knowledge shall my righteous servant justify many; for he shall bear their iniquities. Therefore will I divide him a portion with the great, and he shall divide the spoil with the strong; because he hath poured out his soul unto death: and he was numbered with the transgressors; and he bare the sin of many, and made intercession for the transgressors.', 'ramadan-2026' ),
                __( 'The suffering servant described in this passage was despised, rejected, and familiar with pain. Yet it is with His wounds that we are healed. He was silent when He was oppressed, taken away, and killed. He was neither deceitful nor violent. Yet it was God\'s will that He be crushed, for He bore the sins of many and makes intercession for us before the Father. Isaiah\'s prophecy would be fulfilled in the life and death of Jesus hundreds of years later. God was intentional in His plan for redemption.

- Pray for many in this place to grasp the importance of the prophecy found in Isaiah 52 and 53.
- Pray for the realization that Jesus\' fulfillment of this prophecy testifies to the claims He made to be the Son of God. May they marvel at a God who is able to put all things in place at the perfect time.', 'ramadan-2026' ),
                __( 'Humans sin; we all go our own way. A Holy God could not tolerate our sinfulness and our relationship with Him is broken. One result of this brokenness is the suffering and pain we experience in this life. However, we have hope because God had a plan to send His servant to bear our sins for us. He sent Jesus to intercede for us so that relationship might be restored.

- Pray that Muslims would understand how this prophecy relates to them and that it pointed to the coming of Jesus. Pray that this foretelling would create a vivid picture of the sacrifice Jesus made for us.', 'ramadan-2026' ),
                __( 'Khadija cried out, "God doesn\'t see. He doesn\'t care. My children are mocked at school. My husband has abandoned us. We sleep in the car. I can\'t get a job because of lies that are being told about me." Khadija\'s cry reflects the pain of those who feel unseen and abandoned.

O Jesus, thank you that you do see, and not only do you see, you understand. You have been rejected, lied about, mocked. You lived homeless and were a refugee as a baby. Thank you for carrying our sorrows. You not only know the sin of the world, but they were all laid upon you on the cross. Thank you, Lord, that you received each lash of the whip, each tearing of the thorn, each nail, knowing that these very wounds would heal us. Please Lord, let Muslims see that you are not a God who remains far away waiting for them to rescue themselves with their self-righteousness. You came, knowing that we were sinners and helpless, and you suffered so that we could live. Please Lord, may Muslims see this irresistible love.', 'ramadan-2026' ),
                __( 'Approximately 700 years before the Word came to earth as Jesus, the Spirit inspired the prophet Isaiah to write clearly about the One who would come to redeem us.

- May Christians share this passage with those who do not yet believe, revealing God\'s loving deliberate plan to come and rescue us Himself.
- Pray for God\'s children in this place to be willing to boldly speak the difficult-to-hear news that "all have gone astray." There are none who are righteous. May believers quickly follow that piercing truth with the gospel that God Himself came to bear our sin. He chose death so that we could live.', 'ramadan-2026' ),
                __( 'Hebrews 4:15-16 says, "For we do not have a high priest who is unable to empathize with our weaknesses, but we have one who has been tempted in every way, just as we are – yet he did not sin. Let us then approach God\'s throne of grace with confidence so that we may receive mercy and find grace to help us in our time of need."

There is only One who has never sinned. He chose to live as a human on this earth, understanding and carrying our pain, even unto death. He made a way for us to come right into the throne room of the King where we find grace to help us.

Thank the Lord for the incredible privilege we have to enter into His throne room to intercede on behalf of Muslims, not because of anything we have done but solely because of His grace.', 'ramadan-2026' ),
            ],
            //day 14
            [
                __( 'Day 14: The Promised Savior', 'ramadan-2026' ),
                __( 'Isaiah 9:1-7
Nevertheless the dimness shall not be such as was in her vexation, when at the first he lightly afflicted the land of Zebulun and the land of Naphtali, and afterward did more grievously afflict her by the way of the sea, beyond Jordan, in Galilee of the nations. The people that walked in darkness have seen a great light: they that dwell in the land of the shadow of death, upon them hath the light shined. Thou hast multiplied the nation, and not increased the joy: they joy before thee according to the joy in harvest, and as men rejoice when they divide the spoil. For thou hast broken the yoke of his burden, and the staff of his shoulder, the rod of his oppressor, as in the day of Midian. For every battle of the warrior is with confused noise, and garments rolled in blood; but this shall be with burning and fuel of fire. For unto us a child is born, unto us a son is given: and the government shall be upon his shoulder: and his name shall be called Wonderful, Counsellor, The mighty God, The everlasting Father, The Prince of Peace. Of the increase of his government and peace there shall be no end, upon the throne of David, and upon his kingdom, to order it, and to establish it with judgment and with justice from henceforth even for ever. The zeal of the LORD of hosts will perform this.

Luke 1:26-38
And in the sixth month the angel Gabriel was sent from God unto a city of Galilee, named Nazareth, To a virgin espoused to a man whose name was Joseph, of the house of David; and the virgin\\\'s name was Mary. And the angel came in unto her, and said, Hail, thou that art highly favoured, the Lord is with thee: blessed art thou among women. And when she saw him, she was troubled at his saying, and cast in her mind what manner of salutation this should be. And the angel said unto her, Fear not, Mary: for thou hast found favour with God. And, behold, thou shalt conceive in thy womb, and bring forth a son, and shalt call his name JESUS. He shall be great, and shall be called the Son of the Highest: and the Lord God shall give unto him the throne of his father David: And he shall reign over the house of Jacob for ever; and of his kingdom there shall be no end. Then said Mary unto the angel, How shall this be, seeing I know not a man? And the angel answered and said unto her, The Holy Ghost shall come upon thee, and the power of the Highest shall overshadow thee: therefore also that holy thing which shall be born of thee shall be called the Son of God. And, behold, thy cousin Elisabeth, she hath also conceived a son in her old age: and this is the sixth month with her, who was called barren. For with God nothing shall be impossible. And Mary said, Behold the handmaid of the Lord; be it unto me according to thy word. And the angel departed from her.', 'ramadan-2026' ),
                __( 'Isaiah 9 describes a heroic Savior who comes to bring light and peace where there was death, captivity, and darkness. He will reign as the perfect king, full of justice and righteousness. We see the initial fulfillment of this prophecy in Luke 1 as God\'s plan for this Savior begins to unfold. God sends an angelic messenger to announce the upcoming birth of a humble human baby, Jesus. While fully human, the baby carried by Mary was conceived by the power of the Holy Spirit and was the Son of God.

- Pray for Muslims walking in darkness to see the Great Light, Jesus, prophesied in Isaiah.
- Pray that they could experience the joy the angelic messenger expressed when announcing the upcoming birth of the Son of God that marked the fulfillment of this long awaited eternal King who would rule with justice and righteousness.', 'ramadan-2026' ),
                __( 'Humanity is separated from God, walking in darkness. Our Creator God sees our great need and, instead of abandoning us, He draws near, offering us the gift of His only Son. The Light enters the world in a most unexpected way. He becomes one of us.

God, we know that we are not who You created us to be. We are separated from You with no hope of accessing You on our own. We stumble in the darkness, slaves to our sins and hurting one another. We praise You for being the King above all, the One who came down to us and gave us hope, destroying what holds us captive and making us holy. We ask that this land would learn who You are and what You did for us, because You came to set them free, as well.', 'ramadan-2026' ),
                __( 'The Qur\'an denies the Divine Sonship of Christ and the concept of the Trinity. These concepts are common Muslim arguments against Christianity. However, when the Holy Spirit opens eyes, understanding can break through.

Majid had been listening to the witness of a Christian friend and was thinking through his testimony that Jesus was God, come to earth, One with the Father and the Holy Spirit. As he processed aloud he said, "If I believe God is with me here in my city, and at the same time with my mother in the countryside, I recognize that He is not bound to one place. God the Father can be in heaven and God the Son, Jesus, can be on earth, and God the Spirit can live in us. There are not three gods. There is one God, not bound by place. Just as you can hear an audio message and say, \'This is so-and-so\'s voice…\' Jesus is the Spoken Word sent from God that we might hear Him." Majid declared his faith in Jesus that very evening.

- Pray for the Lord to open the eyes and ears of Muslims who have held the argument against the idea of one God in three distinct Persons, Father, Son, and Holy Spirit.
- Pray that the Lord would reveal to them the astounding way the entire Bible points to the coming of the Eternal One, the Son of God, given as a sacrifice for us.', 'ramadan-2026' ),
                __( 'Christians play a key role in showing Muslims, who are walking in darkness, the bright light of Christ in them.

- Pray that they may have joy, peace, and wisdom while reaching this land.
- Pray that though the concept of one God in three Persons is difficult to grasp and share, may they delight in its mystery and truth. May their faith in the truth of this profound reality be a beacon of light that draws those walking in darkness.', 'ramadan-2026' ),
                __( '1 Corinthians 2:6-16 describes the wisdom of God, hidden in a mystery, that God determined before the ages for our glory: "What no eye has seen, nor ear heard, nor the heart of man imagined, what God has prepared for those who love him." Yet, "these things God has revealed to us through the Spirit." Paul explains that, "The natural person does not accept the things of the Spirit of God, for they are folly to him, and he is not able to understand them because they are spiritually discerned." He concludes, "\'For who has understood the mind of the Lord so as to instruct him?\' But we have the mind of Christ."

Pray that the Lord will reveal to Muslims the mystery that can only be understood by revelation of the Spirit of God. Ask that they would find peace in recognizing we cannot fully understand God\'s mind, yet rejoice that through Christ we are given the mind of Christ.', 'ramadan-2026' ),
            ],
            //day 15
            [
                __( 'Day 15: The Birth of Jesus', 'ramadan-2026' ),
                __( 'Luke 2:1-20
And it came to pass in those days, that there went out a decree from Caesar Augustus, that all the world should be taxed. (And this taxing was first made when Cyrenius was governor of Syria.) And all went to be taxed, every one into his own city. And Joseph also went up from Galilee, out of the city of Nazareth, into Judaea, unto the city of David, which is called Bethlehem; (because he was of the house and lineage of David:) To be taxed with Mary his espoused wife, being great with child. And so it was, that, while they were there, the days were accomplished that she should be delivered. And she brought forth her firstborn son, and wrapped him in swaddling clothes, and laid him in a manger; because there was no room for them in the inn. And there were in the same country shepherds abiding in the field, keeping watch over their flock by night. And, lo, the angel of the Lord came upon them, and the glory of the Lord shone round about them: and they were sore afraid. And the angel said unto them, Fear not: for, behold, I bring you good tidings of great joy, which shall be to all people. For unto you is born this day in the city of David a Saviour, which is Christ the Lord. And this shall be a sign unto you; Ye shall find the babe wrapped in swaddling clothes, lying in a manger. And suddenly there was with the angel a multitude of the heavenly host praising God, and saying, Glory to God in the highest, and on earth peace, good will toward men. And it came to pass, as the angels were gone away from them into heaven, the shepherds said one to another, Let us now go even unto Bethlehem, and see this thing which is come to pass, which the Lord hath made known unto us. And they came with haste, and found Mary, and Joseph, and the babe lying in a manger. And when they had seen it, they made known abroad the saying which was told them concerning this child. And all they that heard it wondered at those things which were told them by the shepherds. But Mary kept all these things, and pondered them in her heart. And the shepherds returned, glorifying and praising God for all the things that they had heard and seen, as it was told unto them.', 'ramadan-2026' ),
                __( 'God is both just and loving. In this passage, we see that Jesus—the Anointed One and God Himself—enters the world in humility. He is born not into wealth or power, but into obscurity. His birth is announced not to kings or religious elites, but to shepherds—men of the lowest social status. In doing so, God reveals His heart: Jesus did not come only for the rich, the powerful, or the respected, but also for the poor, the weak, the overlooked, and sinners.

Jesus is Lord, Messiah, Savior, and King. He is sovereign over all things, the promised Anointed One, the only Rescuer, and the everlasting King from the line of David.

- Praise God and give him glory for doing what we could never do for ourselves—reconciling us to Himself.
- Pray for many in this place to receive God\'s offer of peace to those who place their trust in His Son, who came to us as a baby.
- Because of God\'s great love and mercy, pray that He would draw the Muslims of this land to Himself. May they recognize Jesus as Savior, Anointed One, and God, giving Him all glory and honor.', 'ramadan-2026' ),
                __( 'This passage reminds us that while people may believe they possess power and control, it is God alone who rules over all things. Caesar Augustus issued a decree for a census, setting in motion the movement of thousands of people—likely for taxation and military purposes. Yet unknowingly, he was serving God\'s greater purpose: that the Messiah from the line of David would be born in the town of David.

The shepherds show us the beauty of responding to God\'s revealed truth. Though initially afraid, they believed what God had spoken, went to Bethlehem, and saw with their own eyes. Then they proclaimed what they had witnessed. Joy follows those who listen to God, trust His Word, and speak of what He has done.

- Pray that people in this place would acknowledge the sovereignty of God in their own lives. May they find peace and refuge in a God who rules over all things.
- Pray also that they would find joy in trusting His Word and it would overflow to those around them.', 'ramadan-2026' ),
                __( 'Amira heard about the biblical account of Jesus\' birth and said, "We have that same story in the Qur\'an. Mary was a virgin and God breathed a baby alive in her. People didn\'t believe that she had never been with a man and so they drove her out of the village. She was under a date tree and ate the dates which gave her strength. When Jesus was an infant, he spoke to people telling them that his mother had done nothing wrong."

Muslims have a sura in the Qur\'an about Miriam (Mary). There are also several other writings about Mary and various versions of the story circulate. All the stories agree on the virgin birth, but none recognize Jesus as divine. Muslims believe that Jesus is a prophet with his own miracles just like Moses or Elijah.

Oftentimes the similarities between the stories in the Bible and the stories in the Qur\'an lead to the belief that the Muslim faith and the Christian faith are essentially the same.

- Pray that Muslims would be able to clearly understand the crucial difference between Jesus being a prophet and Jesus being the Son of God come to save us from our sins.
- Ask that this revelation would trigger a chain reaction of faith exploration and that would ultimately lead them to the truth of the Gospel.', 'ramadan-2026' ),
                __( 'As Christians interact with Muslims, pray that they would engage in conversations marked by clarity, humility, and grace. Ask God to help them thoughtfully discuss the differences between the Biblical and Islamic accounts of Jesus\' birth, and to clearly explain the implications of Luke\'s Gospel concerning the divinity of Christ.

- Pray that believers would have favor with those they speak with, and that they would be filled with love that speaks the truth gently and courageously.
- Ask that fear of difficult conversations or fear of appearing offensive would not silence the gospel, but that Christians would trust God to work through faithful witness.', 'ramadan-2026' ),
                __( 'Philippians 2:6-8 describes Jesus by saying, "Who, though he was in the form of God, did not count equality with God a thing to be grasped, but emptied himself, by taking the form of a servant, being born in the likeness of men. And being found in human form, he humbled himself by becoming obedient to the point of death, even death on a cross."

Pray that those accepting the divinity of Jesus would see His submission to the Father and seek to follow His example. Ask that they would become obedient disciples eager to know more about the character and nature of the Messiah.', 'ramadan-2026' ),
            ],
            //day 16
            [
                __( 'Day 16: John Testifies of the Savior', 'ramadan-2026' ),
                __( 'Mark 1:1-8
The beginning of the gospel of Jesus Christ, the Son of God; As it is written in the prophets, Behold, I send my messenger before thy face, which shall prepare thy way before thee. The voice of one crying in the wilderness, Prepare ye the way of the Lord, make his paths straight. John did baptize in the wilderness, and preach the baptism of repentance for the remission of sins. And there went out unto him all the land of Judaea, and they of Jerusalem, and were all baptized of him in the river of Jordan, confessing their sins. And John was clothed with camel\\\'s hair, and with a girdle of a skin about his loins; and he did eat locusts and wild honey; And preached, saying, There cometh one mightier than I after me, the latchet of whose shoes I am not worthy to stoop down and unloose. I indeed have baptized you with water: but he shall baptize you with the Holy Ghost.

John 1:29-34
The next day John seeth Jesus coming unto him, and saith, Behold the Lamb of God, which taketh away the sin of the world. This is he of whom I said, After me cometh a man which is preferred before me: for he was before me. And I knew him not: but that he should be made manifest to Israel, therefore am I come baptizing with water. And John bare record, saying, I saw the Spirit descending from heaven like a dove, and it abode upon him. And I knew him not: but he that sent me to baptize with water, the same said unto me, Upon whom thou shalt see the Spirit descending, and remaining on him, the same is he which baptizeth with the Holy Ghost. And I saw, and bare record that this is the Son of God.', 'ramadan-2026' ),
                __( 'God intentionally sent John to prepare the way for the public ministry of Jesus. John\'s call to repentance awakened hearts to their need for forgiveness and readied people to receive the Messiah.

It was also the Father\'s will that Jesus submit to baptism, not because He needed repentance, but to reveal His humility and model His obedience. At that moment, God made Jesus\' identity clear to John: He is the Lamb of God who takes away the sin of the world and the Son of God sent from heaven.

- Pray that God would prepare the hearts of people in this land to receive the message of Christ.
- Pray that Jesus\' true identity would be revealed to those seeking to follow Him. May the contrast between the titles Jesus held and the actions Jesus took cause wonder and curiosity.', 'ramadan-2026' ),
                __( 'John exemplifies a person called by God for a specific purpose. He submits to God\'s direction and is used to proclaim repentance and obedience through baptism.

This passage also reveals the universal problem of sin; a problem that no person can resolve on their own. Many in the surrounding region responded by recognizing their need for repentance and taking action, submitting themselves to the call of God.

- Pray that the same conviction of sin that drew the people into the wilderness to be baptized by John would lead people in this land to seek freedom from the burden of sin and the forgiveness only God provides.
- Pray that men and women here would have real encounters with the living God, and that these encounters would compel them to boldly testify to the Son of God and His work of grace in their lives.', 'ramadan-2026' ),
                __( 'One of the Qur\'anic verses that many Muslims memorize and often quote says, "He [God] neither begets nor is born." From their earliest education they are taught to deny that Jesus is the Son of God.

- Pray for the Holy Spirit, who bears witness about Jesus (John 15:26), to work in the hearts of men and women across the Muslim world to understand the Truth of what all the Old Testament prophets were teaching us about who Jesus would be.
- Pray for hearts to remember the lamb they sacrifice every year in honor of God\'s intervention for Abraham and for them to understand what it means when John says, "Behold, the Lamb of God who takes away the sin of the world!"', 'ramadan-2026' ),
                __( 'Miriam, an elderly lady, put her faith in Jesus and simple step by simple step began to follow Him. She had no idea what He had in store for the day of her baptism. She had read about Jesus\' baptism and His promise to baptize with the Holy Spirit. She didn\'t know that she would go into the water legally blind and that she would emerge from the water with her eyesight fully restored. Even better, this miraculous healing emboldened her to share her faith with her children and grandchildren.

Pray for God to move miraculously in the lives of Muslim people. Pray that the stories of His provision would reach the ears of those ready to come to Him.', 'ramadan-2026' ),
                __( 'Isaiah 40:3-5 is the Old Testament passage echoed in Mark 1. It describes a wilderness responding to God\'s call, making way for the Lord to move. We pray for the reality of these promises in this place:

In the spiritual wilderness of this place, O God, prepare the way for your Messiah.

We ask you to make straight in the desert a highway for our God. Provide clear paths for people to come to know You. We pray for copies of Scripture to be accessible and distributable in this place.

We pray for every valley of fear, despair, doubt, and hopelessness that hinders people from seeking Christ to be lifted up.

We pray for every mountain and hill of false religion, persecution, spiritual pacts and bonds, and work of the enemy to be made low.

May the glory of the Lord be revealed and all flesh of this land see it together.', 'ramadan-2026' ),
            ],
            //day 17
            [
                __( 'Day 17: Temptation and Ministry of Jesus', 'ramadan-2026' ),
                __( 'Luke 4:1-22
And Jesus being full of the Holy Ghost returned from Jordan, and was led by the Spirit into the wilderness, Being forty days tempted of the devil. And in those days he did eat nothing: and when they were ended, he afterward hungered. And the devil said unto him, If thou be the Son of God, command this stone that it be made bread. And Jesus answered him, saying, It is written, That man shall not live by bread alone, but by every word of God. And the devil, taking him up into an high mountain, shewed unto him all the kingdoms of the world in a moment of time. And the devil said unto him, All this power will I give thee, and the glory of them: for that is delivered unto me; and to whomsoever I will I give it. If thou therefore wilt worship me, all shall be thine. And Jesus answered and said unto him, Get thee behind me, Satan: for it is written, Thou shalt worship the Lord thy God, and him only shalt thou serve. And he brought him to Jerusalem, and set him on a pinnacle of the temple, and said unto him, If thou be the Son of God, cast thyself down from hence: For it is written, He shall give his angels charge over thee, to keep thee: And in their hands they shall bear thee up, lest at any time thou dash thy foot against a stone. And Jesus answering said unto him, It is said, Thou shalt not tempt the Lord thy God. And when the devil had ended all the temptation, he departed from him for a season. And Jesus returned in the power of the Spirit into Galilee: and there went out a fame of him through all the region round about. And he taught in their synagogues, being glorified of all. And he came to Nazareth, where he had been brought up: and, as his custom was, he went into the synagogue on the sabbath day, and stood up for to read. And there was delivered unto him the book of the prophet Esaias. And when he had opened the book, he found the place where it was written, The Spirit of the Lord is upon me, because he hath anointed me to preach the gospel to the poor; he hath sent me to heal the brokenhearted, to preach deliverance to the captives, and recovering of sight to the blind, to set at liberty them that are bruised, To preach the acceptable year of the Lord. And he closed the book, and he gave it again to the minister, and sat down. And the eyes of all them that were in the synagogue were fastened on him. And he began to say unto them, This day is this scripture fulfilled in your ears. And all bare him witness, and wondered at the gracious words which proceeded out of his mouth. And they said, Is not this Joseph\\\'s son?', 'ramadan-2026' ),
                __( '- This passage teaches us that God became fully man and was tempted in every way we are, yet was without sin. Pray for this reality to comfort and challenge believers as they become more like Jesus to resist Satan\'s temptations for power and comfort in their lives.
- We also learn that God has permitted Satan to have some level of authority on this side of eternity. Pray for people in this place to rightly attribute evil to Satan, but to not misunderstand that Satan\'s power is limited and his defeat has already been secured.
- We learn that Jesus is the complete fulfillment of all the prophecies of Scripture. Pray for Muslim eyes and hearts to be opened to see the supremacy of Christ over every other prophet and holy man. May their hearts swell as they rightly see Him for who He has revealed Himself to be through Scripture.', 'ramadan-2026' ),
                __( 'This passage teaches us that all of the bad news that humanity faces is firmly and finally replaced with good news through Christ.

- Pray for the poor in this land, that they would respond and find hope in the Good News Jesus offers.
- Pray for those who are in bondage to evil spirits in this place. Pray for them to experience the liberty Jesus provides and to be set free from all of the ways the enemy is trying to keep them in darkness.
- Pray for miracles of healing for those who are blind, both spiritually and physically. Pray for the name of Jesus to be revered and honored through these healings.
- Pray for the oppressed -- women in abusive marriages, minorities in exploitative work environments, those in slavery, and trafficked people -- to be given liberty. Pray for the oppressors to repent and to find forgiveness of sins in Christ.', 'ramadan-2026' ),
                __( 'Riadh was in his early twenties when he became curious about Christianity. Despite being young, he was financially independent and affluent. He was running a business and making a lot of money. The Christian who was sharing the Gospel with him was excited about Riadh\'s hunger. But then Riadh started to back away. When the Christian asked him why, he said, "I made a pact with good Muslim spirits, but they are the ones who have made me rich. They don\'t want me to continue. I think I need a break from pursuing this."

Riadh had taken the enemy up on his offer to receive authority and glory from him and with it came complete bondage. Pray for people in this land to be set free from every pact and agreement they have made with darkness and to come into the glorious and freeing light of Christ.', 'ramadan-2026' ),
                __( 'Pray for Christians in this place who are seeking to share Good News with Muslims to operate in the power of the Spirit. Pray for their hearts to be meditating on Scripture so that they can respond to Satan\'s temptations with God\'s Word. Pray for the Church to continue to live out the fulfillment of the Isaiah scroll in their land -- to be ministers of justice, mercy, compassion, and liberators.', 'ramadan-2026' ),
                __( 'Jesus fasted for 40 days and the rest of Luke 4 flows out of that time with the Father. Pray for Christians in this place to practice true fasting and for it to be a testimony to Muslims who don\'t know that Christians fast nor what true fasting really means:

Pray for the fasting that God calls for (Isaiah 58:6-8): to loose the bonds of wickedness, to undo the straps of the yoke, to let the oppressed go free, and to break every yoke. Pray for people in this land to share their bread with the hungry and bring the homeless poor into their homes. When they see the naked, give them means to cover them.

Then bless true fasters in this land. That their light would break forth like the dawn, and healing spring up speedily. We ask for their righteousness to go before them, and the glory of the Lord to be their rear guard.

May men and women in this land experience the promise of Isaiah 58:10-11, "If you pour yourself out for the hungry and satisfy the desire of the afflicted, then shall your light rise in the darkness and your gloom be as the noonday. And the Lord will guide you continually and satisfy your desire in scorched places and make your bones strong. And you shall be like a watered garden, like a spring of water whose waters do not fail."', 'ramadan-2026' ),
            ],
            //day 18
            [
                __( 'Day 18: Jesus Heals and Forgives', 'ramadan-2026' ),
                __( 'Luke 5:17-32
And it came to pass on a certain day, as he was teaching, that there were Pharisees and doctors of the law sitting by, which were come out of every town of Galilee, and Judaea, and Jerusalem: and the power of the Lord was present to heal them. And, behold, men brought in a bed a man which was taken with a palsy: and they sought means to bring him in, and to lay him before him. And when they could not find by what way they might bring him in because of the multitude, they went upon the housetop, and let him down through the tiling with his couch into the midst before Jesus. And when he saw their faith, he said unto him, Man, thy sins are forgiven thee. And the scribes and the Pharisees began to reason, saying, Who is this which speaketh blasphemies? Who can forgive sins, but God alone? But when Jesus perceived their thoughts, he answering said unto them, What reason ye in your hearts? Whether is easier, to say, Thy sins be forgiven thee; or to say, Rise up and walk? But that ye may know that the Son of man hath power upon earth to forgive sins, (he said unto the sick of the palsy,) I say unto thee, Arise, and take up thy couch, and go into thine house. And immediately he rose up before them, and took up that whereon he lay, and departed to his own house, glorifying God. And they were all amazed, and they glorified God, and were filled with fear, saying, We have seen strange things to day. And after these things he went forth, and saw a publican, named Levi, sitting at the receipt of custom: and he said unto him, Follow me. And he left all, rose up, and followed him. And Levi made him a great feast in his own house: and there was a great company of publicans and of others that sat down with them. But their scribes and Pharisees murmured against his disciples, saying, Why do ye eat and drink with publicans and sinners? And Jesus answering said unto them, They that are whole need not a physician; but they that are sick. I came not to call the righteous, but sinners to repentance.', 'ramadan-2026' ),
                __( '- This passage teaches us that God responds to faith. Pray for faith to grow among the people of this land; that even if they have faith the size of a mustard seed, God would take that faith and do extraordinary things for His glory.
- This passage also teaches us that God is not afraid to sit with sinners. Though it was scandalous, Jesus went to the homes of sinners and tax collectors and ate with them. Pray for Christians who are willing to live out this example. Pray for believing men and women to build genuine, loving relationships with Muslim families—to eat with them, share life with them, and bring them the good news of Jesus.', 'ramadan-2026' ),
                __( 'This passage teaches us that our faith and actions impact others. Pray for God to raise up brave men and women among this people group who would go to extraordinary lengths to bring their friends to Christ\'s feet.

"Did I tell you I went on the pilgrimage to Mecca this year?" Rania proudly asked. "Yes, Auntie, you did." "Hmmm," Rania continued unfazed, "I spent 21 days in Saudi Arabia." Rania then went on to list other good deeds she had done. Religion, with its lists of dos and don\'ts, has a way of slanting our orientation and causing us to focus on how much better we are than other people we are comparing ourselves to.

Jesus said, "Those who are well have no need of a physician, but those who are sick. I have not come to call the righteous but sinners to repentance" (Luke 5:32). Not from any sense of superiority, but in a spirit of humility, pray for God to reveal spiritual sickness to Muslims in this land and to stir them to seek out the Good Physician who alone can fully heal their sin problem.', 'ramadan-2026' ),
                __( 'Muslims\' objections to Christ sound very much like the Pharisees of Jesus\' day. "Who is this who speaks blasphemies? Who can forgive sins but God alone?" They don\'t object to the idea that He did miracles; rather, they object to the authority He claimed -- that He is the Son of God and has the power to forgive sins.

Pray for miracles to take place in this land that confirm the authority of Jesus as the Son of God. Thank Him for the many signs and wonders He is pouring out all over the Muslim world and pray for those to result in families, neighborhoods, friend groups, and repenting of self-righteousness and clinging to Christ\'s true righteousness.', 'ramadan-2026' ),
                __( 'We know very little about the four people who carried the paralytic to Jesus, but we can assume that they did this with significant effort and sacrifice on their part.

- Pray for God to raise up believers who will work together to bring the good news to this people. Pray for the unity among believers, that collaboration would be one of the means God uses to bring the gospel to this land.
- Pray that every way the enemy has sown disunity through fear, persecution, or mistrust would be replaced with joyful cooperation and unified effort to bring the lost of this land to the feet of Jesus.', 'ramadan-2026' ),
                __( 'Another account of a lame man being healed is found in Acts chapter 3.

"And Peter directed his gaze at him, as did John, and said, "Look at us." And he fixed his attention on them, expecting to receive something from them. But Peter said, "I have no silver and gold, but what I do have I give to you. In the name of Jesus Christ of Nazareth, rise up and walk." And he took him by the right hand and raised him up, and immediately his feet and ankles were made strong."

Lord, we pray for the continuation of the work you began through the early church today in this land and among this people. Just as this healing led to Peter and John having to publicly proclaim Christ, may miracles today result in the bold testimony to the One who heals. May Christ be treasured and revered among the people of this place.', 'ramadan-2026' ),
            ],
            //day 19
            [
                __( 'Day 19: Jesus\' Power', 'ramadan-2026' ),
                __( 'Mark 4:35-5:20
And the same day, when the even was come, he saith unto them, Let us pass over unto the other side. And when they had sent away the multitude, they took him even as he was in the ship. And there were also with him other little ships. And there arose a great storm of wind, and the waves beat into the ship, so that it was now full. And he was in the hinder part of the ship, asleep on a pillow: and they awake him, and say unto him, Master, carest thou not that we perish? And he arose, and rebuked the wind, and said unto the sea, Peace, be still. And the wind ceased, and there was a great calm. And he said unto them, Why are ye so fearful? how is it that ye have no faith? And they feared exceedingly, and said one to another, What manner of man is this, that even the wind and the sea obey him? And they came over unto the other side of the sea, into the country of the Gadarenes. And when he was come out of the ship, immediately there met him out of the tombs a man with an unclean spirit, Who had his dwelling among the tombs; and no man could bind him, no, not with chains: Because that he had been often bound with fetters and chains, and the chains had been plucked asunder by him, and the fetters broken in pieces: neither could any man tame him. And always, night and day, he was in the mountains, and in the tombs, crying, and cutting himself with stones. But when he saw Jesus afar off, he ran and worshipped him, And cried with a loud voice, and said, What have I to do with thee, Jesus, thou Son of the most high God? I adjure thee by God, that thou torment me not. For he said unto him, Come out of the man, thou unclean spirit. And he asked him, What is thy name? And he answered, saying, My name is Legion: for we are many. And he besought him much that he would not send them away out of the country. Now there was there nigh unto the mountains a great herd of swine feeding. And all the devils besought him, saying, Send us into the swine, that we may enter into them. And forthwith Jesus gave them leave. And the unclean spirits went out, and entered into the swine: and the herd ran violently down a steep place into the sea, (they were about two thousand;) and were choked in the sea. And they that fed the swine fled, and told it in the city, and in the country. And they went out to see what it was that was done. And they come to Jesus, and see him that was possessed with the devil, and had the legion, sitting, and clothed, and in his right mind: and they were afraid. And they that saw it told them how it befell to him that was possessed with the devil, and also concerning the swine. And they began to pray him to depart out of their coasts. And when he was come into the ship, he that had been possessed with the devil prayed him that he might be with him. Howbeit Jesus suffered him not, but saith unto him, Go home to thy friends, and tell them how great things the Lord hath done for thee, and hath had compassion on thee. And he departed, and began to publish in Decapolis how great things Jesus had done for him: and all men did marvel.', 'ramadan-2026' ),
                __( 'Jesus\' authority extends over nature.

- Pray for the people of this place to turn to Jesus, the One with authority over both the literal and metaphorical storms in life.

Jesus also has authority over evil spirits.

- Pray for every spiritual pact people in this land have made under folk Islam to be broken in the name of Jesus.
- Pray for Muslims who rely on offering sacrifices at the graves of dead \'saints\' to receive blessings. Pray for all of the effects of charms, pins sewn into clothing, wall hangings, and superstitious incantations to be broken in the name of Jesus. Pray for people in this land to experience the freedom of Jesus.', 'ramadan-2026' ),
                __( 'This passage teaches us how prone humanity is to respond in fear instead of living in faith. Jesus\' disciples were afraid of the storm. The people of the Gerasenes were afraid of the demoniac, then they were afraid of his healing.

- Pray for the power of fear to be broken in the lives of the people in this place.
- Pray for Christians who are still living in fear to experience the freedom that Jesus came to give them. Pray that they would be encouraged and empowered to walk in faith instead.
- Pray for the lost who are in bondage to the fear of eternal damnation when asking questions, thinking critically, reading the Bible, or exploring the claims of Christ. Pray for these bonds to be broken, for faith to grow in them that Jesus is exactly who He says He is in the New Testament.', 'ramadan-2026' ),
                __( 'Though it may be contrary to official Islamic doctrine, throughout the Muslim world, many people enter into the spiritual realm to seek blessing, protection, and success from spirits. Sarra had made a pact with one of these spirits to help her succeed in her studies. When she met a Christian and started going to their small home church, the spirit she made a pact with didn\'t like it. Eventually, she found that she was failing in school. She decided grades and success in school were of greater worth than Jesus and walked away from Him and from her Christian family. The small house church would continue to pray for her even though she wasn\'t in attendance. She sensed their prayers and would call someone in the group and tell them to stop praying for her. A couple of years later, the unrelenting grace of Jesus that wouldn\'t give up on her won her heart and she repented and turned back to Him. Pray for many, many more stories of freedom for the people of this land. May there be multitudes who would testify to Jesus\' power and boundless grace.', 'ramadan-2026' ),
                __( 'Right now there are Christians in this land who are facing intense storms in life: threats from family members, the possibility of losing children, financial uncertainty, unemployment, and being thrown in prison because of their faith in Christ. There are also believers facing storms because of the fallen world we live in: cancer, depression, underemployment, strained relationships, and loneliness.

- Pray for Jesus\' words, "Peace! Be still!" to comfort them in their storms and to grow faith in their hearts. Pray that the peace of Christ would be strongly felt by them today and each day.', 'ramadan-2026' ),
                __( 'Today we pray Ephesians 6:10-20 for our brothers and sisters among this people -- that they would be strong in the Lord and in the strength of His might. May they put on the whole armor of God that they would be able to stand against the schemes of the devil. For we do not wrestle against flesh and blood, but against the rulers, against the authorities, against the cosmic powers over this present darkness, against the spiritual forces of evil in the heavenly places.

May the people of this land take up the whole armor of God, that they would be able to withstand in the evil day, and having done all, to stand firm. May they stand, therefore, having fastened on the belt of truth and having put on the breastplate of righteousness. And as shoes for their feet, put on the readiness given by the gospel of peace.

In all circumstances may the people of this land take up the shield of faith with which they can extinguish all the flaming darts of the evil one. Put the helmet of salvation firmly on their heads and the sword of the Spirit in their hands, which is the Word of God, praying at all times in the Spirit with all prayer and supplication.

So Lord, to this end, keep us alert with all perseverance, making supplication for all the saints. We pray for our brothers and sisters in this land that words would be given to them to open their mouths boldly to proclaim the mystery of the gospel.', 'ramadan-2026' ),
            ],
            //day 20
            [
                __( 'Day 20: Forgiveness and Sacrifice', 'ramadan-2026' ),
                __( 'Luke 18:9-30
And he spake this parable unto certain which trusted in themselves that they were righteous, and despised others: Two men went up into the temple to pray; the one a Pharisee, and the other a publican. The Pharisee stood and prayed thus with himself, God, I thank thee, that I am not as other men are, extortioners, unjust, adulterers, or even as this publican. I fast twice in the week, I give tithes of all that I possess. And the publican, standing afar off, would not lift up so much as his eyes unto heaven, but smote upon his breast, saying, God be merciful to me a sinner. I tell you, this man went down to his house justified rather than the other: for every one that exalteth himself shall be abased; and he that humbleth himself shall be exalted. And they brought unto him also infants, that he would touch them: but when his disciples saw it, they rebuked them. But Jesus called them unto him, and said, Suffer little children to come unto me, and forbid them not: for of such is the kingdom of God. Verily I say unto you, Whosoever shall not receive the kingdom of God as a little child shall in no wise enter therein. And a certain ruler asked him, saying, Good Master, what shall I do to inherit eternal life? And Jesus said unto him, Why callest thou me good? none is good, save one, that is, God. Thou knowest the commandments, Do not commit adultery, Do not kill, Do not steal, Do not bear false witness, Honour thy father and thy mother. And he said, All these have I kept from my youth up. Now when Jesus heard these things, he said unto him, Yet lackest thou one thing: sell all that thou hast, and distribute unto the poor, and thou shalt have treasure in heaven: and come, follow me. And when he heard this, he was very sorrowful: for he was very rich. And when Jesus saw that he was very sorrowful, he said, How hardly shall they that have riches enter into the kingdom of God! For it is easier for a camel to go through a needle\\\'s eye, than for a rich man to enter into the kingdom of God. And they that heard it said, Who then can be saved? And he said, The things which are impossible with men are possible with God. Then Peter said, Lo, we have left all, and followed thee. And he said unto them, Verily I say unto you, There is no man that hath left house, or parents, or brethren, or wife, or children, for the kingdom of God\\\'s sake, Who shall not receive manifold more in this present time, and in the world to come life everlasting.', 'ramadan-2026' ),
                __( '- God exalts the humble and humbles those who exalt themselves. Pray for the poor among this people, those looked down upon because of their lack of education, their family name, or having the "wrong" skin color or a disability. Pray they will have an opportunity to hear about the One who exalts the humble, for them to have their story rewritten by the good news of Jesus.
- This passage also teaches us that God turns impossibilities into possibilities. Nothing is too hard for Him. As we look at the daunting needs of the Muslim world, stir Christian hearts to trust that they are not impossible for God.', 'ramadan-2026' ),
                __( 'This passage teaches how prone humanity is to compare ourselves to one another instead of a Holy God. We think if we are better than some arbitrary standard we set for ourselves, then we\'ll be accepted by God. Pray for these illusions to be broken and for God to humble those who think their good deeds will somehow make them acceptable to a holy and perfect God on their own merits.

The rich ruler illustrates this reality. He had a checklist of laws he believed he was keeping, yet he still sensed something was missing. Jesus went after his heart and exposed what he was truly worshipping.

- Pray for Muslims to feel like something is still lacking no matter how well they follow the rules.
- Pray for Jesus to penetrate their hearts, that they would turn from self-righteousness and to Jesus and His Kingdom.', 'ramadan-2026' ),
                __( 'All who acknowledge God\'s existence, including Muslims, long for God to show them mercy, while also desiring His judgment to fall on those who have wronged them. Many struggle to recognize the depth of their own sin and, as a result, are taught to evaluate themselves in comparison to others rather than before a holy God. This can lead to standing in judgment over those they perceive to be less worthy of the Lord\'s grace.

Islam offers no resolution for how God can be perfectly merciful and perfectly just in the way the cross of Christ does. Pray that humility would lead Muslims in this land to seek the words and teachings of Jesus, and that they would come to treasure Him above their own self-earned righteousness.', 'ramadan-2026' ),
                __( 'Amira was an intelligent, educated woman and a devoted Muslim with a successful career. She had much to lose when she heard the good news of Jesus and became aware of His loving pursuit of her. In time, He won her heart, and she counted everything as loss for the sake of knowing Christ (Philippians 3:8). Gaining Jesus became more precious to her than career, family, worldly success, or comfort.

Pray for Amira and for many more like her in this land who have taken Jesus at His word: "Truly, I say to you, there is no one who has left house or wife or brothers or parents or children, for the sake of the kingdom of God, who will not receive many times more in this time, and in the age to come eternal life." (Luke 18:29-30). Pray that they would be strengthened, encouraged, and sustained as they follow Him faithfully.', 'ramadan-2026' ),
                __( 'Lord, we know that all of us were dead in our trespasses and sins. We once walked in those sins, following the course of this world, following the prince of the power of the air, and the spirit that is now at work in the sons of disobedience. We once lived in the passions of our flesh, carrying out the desires of the body and the mind, and were by nature children of wrath, like the rest of mankind.

But we pray in faith that, like us, many Muslims in this land would testify to the reality that You, God, being rich in mercy, because of your great love with which you loved us, even when we were dead in our trespasses, made us alive together with Christ. We pray for many to testify that by grace they have been saved, raised up with Christ, and seated with Him in the heavenly places, so that in the coming ages, You might show the immeasurable riches of Your grace and kindness towards us and them in Christ Jesus.

May there be many this year who would say, "By grace we have been saved through faith. And this is not our own doing: it is the gift of God, not a result of works, so that we would have no reason to boast." (Ephesians 2:1-9)', 'ramadan-2026' ),
            ],
            //day 21
            [
                __( 'Day 21: Love God and Love Your Neighbor', 'ramadan-2026' ),
                __( 'Luke 10:25-37
And, behold, a certain lawyer stood up, and tempted him, saying, Master, what shall I do to inherit eternal life? He said unto him, What is written in the law? how readest thou? And he answering said, Thou shalt love the Lord thy God with all thy heart, and with all thy soul, and with all thy strength, and with all thy mind; and thy neighbour as thyself. And he said unto him, Thou hast answered right: this do, and thou shalt live. But he, willing to justify himself, said unto Jesus, And who is my neighbour? And Jesus answering said, A certain man went down from Jerusalem to Jericho, and fell among thieves, which stripped him of his raiment, and wounded him, and departed, leaving him half dead. And by chance there came down a certain priest that way: and when he saw him, he passed by on the other side. And likewise a Levite, when he was at the place, came and looked on him, and passed by on the other side. But a certain Samaritan, as he journeyed, came where he was: and when he saw him, he had compassion on him, And went to him, and bound up his wounds, pouring in oil and wine, and set him on his own beast, and brought him to an inn, and took care of him. And on the morrow when he departed, he took out two pence, and gave them to the host, and said unto him, Take care of him; and whatsoever thou spendest more, when I come again, I will repay thee. Which now of these three, thinkest thou, was neighbour unto him that fell among the thieves? And he said, He that shewed mercy on him. Then said Jesus unto him, Go, and do thou likewise.

Luke 6:27-31
But I say unto you which hear, Love your enemies, do good to them which hate you, Bless them that curse you, and pray for them which despitefully use you. And unto him that smiteth thee on the one cheek offer also the other; and him that taketh away thy cloke forbid not to take thy coat also. Give to every man that asketh of thee; and of him that taketh away thy goods ask them not again. And as ye would that men should do to you, do ye also to them likewise.', 'ramadan-2026' ),
                __( 'This passage teaches us that God is content to let His Word speak for itself. In the New Testament, Jesus is asked 180 direct questions, yet He answers only a handful of them directly. Instead, He often answered through parables.

- Pray for multitudes of Muslims in this region to read God\'s Word and for God to speak to them through it even if they do not have a believer beside them to explain every detail.
- Pray that obedience to the things they learn would result in strong disciples who listen and obey God\'s Word.', 'ramadan-2026' ),
                __( 'People are prone to justify their actions. Someone once said humans are the best prosecutors when it comes to accusing those who have wronged us and the best defense attorneys when it comes to defending our own actions. Pray for the lost in this place to recognize that they cannot even live up to the standards they set for others -- much less God\'s standards. Pray for this awareness and conviction to drive them to seek out a Bible or to talk to a Christian.', 'ramadan-2026' ),
                __( 'Rached grew up in a loving family that felt Islam was an important part of their identity but didn\'t rule every aspect of their lives. They valued education and encouraged Rached to pursue his dreams of becoming an architect. Eventually Rached would grow up to be a nominal Muslim with a worldview that sounded more agnostic than Muslim. When he would meet with a Christian to discuss the Bible, his skepticism would pour out about all religions. Like many nominal Muslims, he prefers to believe that Islam and Christianity are basically the same thing -- some people follow Jesus and some people follow Mohammed. But this passage in Luke 6:27-31 is one Rached, and others like him, can\'t get around. Islam has no equivalent to "love your enemies" and "do good to those who hate you."

- Pray for nominal Muslims in this place to have the opportunity to read the New Testament and to have the capacity to acknowledge the profound differences in the message of the New Testament and the message of the Qur\'an.
- Pray for devout Muslims in this place to ponder what it means to love God with all their heart, soul, strength, and mind and to love their neighbor as themselves. Pray that Muslims would truly seek to love God with their minds -- acknowledging that He created our minds and that we can ask questions and He is more than capable of handling our doubts.', 'ramadan-2026' ),
                __( '- Pray for Christians in this area trying to love Muslims and share the Gospel to live like the Good Samaritan. Pray for them to have opportunities to sacrificially love and serve the needs of those who are different from them.
- Pray that the light of Christians living out Jesus\' command to love their enemies, do good to those who hate them, bless those who curse them, and pray for those who hurt them -- would shine as bright as the noonday sun. Pray for the onlooking Muslim neighbors, coworkers, family members, and friends to be impacted. May they ask questions about where the strength comes from to do such countercultural actions.', 'ramadan-2026' ),
                __( 'Today we will pray Romans 12:9-21 over this land. Ask God to empower Christians from a Muslim background in this place to love one another with brotherly affection and outdo one another in showing honor.

Empower them to rejoice in hope, be patient in tribulation, and be constant in prayer. Give them strength and resources to contribute to the needs of the saints and to practice hospitality.

Strengthen your church to bless those who persecute them, blessing and not cursing. Pray that believers would rejoice with their Muslim neighbors who rejoice at weddings and births. Pray for them to weep with their Muslim neighbors who weep at funerals and times of tragedy.

May Christians in this place live in harmony with one another. May they not be proud, but associate with the lowly. May they repay no one evil for evil, but seek to do what is honorable in the sight of all. And if possible, so far as it depends on them, may they live peaceably with everyone, including family members who persecute them, speak against them, curse them, spit on them, or abuse them. Give them grace to entrust justice to the wrath of God.

And may they find that if their enemy is hungry, they feed him; If he is thirsty, they give him something to drink. We bless believers in this place to not be overcome by evil, but to overcome evil with good.', 'ramadan-2026' ),
            ],
            //day 22
            [
                __( 'Day 22: God Seeks the Lost', 'ramadan-2026' ),
                __( 'Luke 15:1-32
Then drew near unto him all the publicans and sinners for to hear him. And the Pharisees and scribes murmured, saying, This man receiveth sinners, and eateth with them. And he spake this parable unto them, saying, What man of you, having an hundred sheep, if he lose one of them, doth not leave the ninety and nine in the wilderness, and go after that which is lost, until he find it? And when he hath found it, he layeth it on his shoulders, rejoicing. And when he cometh home, he calleth together his friends and neighbours, saying unto them, Rejoice with me; for I have found my sheep which was lost. I say unto you, that likewise joy shall be in heaven over one sinner that repenteth, more than over ninety and nine just persons, which need no repentance. Either what woman having ten pieces of silver, if she lose one piece, doth not light a candle, and sweep the house, and seek diligently till she find it? And when she hath found it, she calleth her friends and her neighbours together, saying, Rejoice with me; for I have found the piece which I had lost. Likewise, I say unto you, there is joy in the presence of the angels of God over one sinner that repenteth. And he said, A certain man had two sons: And the younger of them said to his father, Father, give me the portion of goods that falleth to me. And he divided unto them his living. And not many days after the younger son gathered all together, and took his journey into a far country, and there wasted his substance with riotous living. And when he had spent all, there arose a mighty famine in that land; and he began to be in want. And he went and joined himself to a citizen of that country; and he sent him into his fields to feed swine. And he would fain have filled his belly with the husks that the swine did eat: and no man gave unto him. And when he came to himself, he said, How many hired servants of my father\\\'s have bread enough and to spare, and I perish with hunger! I will arise and go to my father, and will say unto him, Father, I have sinned against heaven, and before thee, And am no more worthy to be called thy son: make me as one of thy hired servants. And he arose, and came to his father. But when he was yet a great way off, his father saw him, and had compassion, and ran, and fell on his neck, and kissed him. And the son said unto him, Father, I have sinned against heaven, and in thy sight, and am no more worthy to be called thy son. But the father said to his servants, Bring forth the best robe, and put it on him; and put a ring on his hand, and shoes on his feet: And bring hither the fatted calf, and kill it; and let us eat, and be merry: For this my son was dead, and is alive again; he was lost, and is found. And they began to be merry. Now his elder son was in the field: and as he came and drew nigh to the house, he heard musick and dancing. And he called one of the servants, and asked what these things meant. And he said unto him, Thy brother is come; and thy father hath killed the fatted calf, because he hath received him safe and sound. And he was angry, and would not go in: therefore came his father out, and intreated him. And he answering said to his father, Lo, these many years do I serve thee, neither transgressed I at any time thy commandment: and yet thou never gavest me a kid, that I might make merry with my friends: But as soon as this thy son was come, which hath devoured thy living with harlots, thou hast killed for him the fatted calf. And he said unto him, Son, thou art ever with me, and all that I have is thine. It was meet that we should make merry, and be glad: for this thy brother was dead, and is alive again; and was lost, and is found.', 'ramadan-2026' ),
                __( 'Three beautiful pictures of lost things are described in this passage. They reveal how much God rejoices when the lost are returned to Him and the extreme lengths He is willing to go in order to find them.

- Pray for Christians all over the world to agree with God about this and to follow their Master in going after them, even at great cost, in order to restore them to God. Pray that today will be a day of great rejoicing in heaven over sinners repenting.', 'ramadan-2026' ),
                __( 'This passage teaches us that there are two lost sons. One son is lost because he rejected relationship with the Father and sinfully wastes his life with reckless living. The other son is lost because he keeps all the rules -- not out of love for the Father, but for what he hopes to gain from him. The world is filled with both kinds of people: those who live in open rebellion like the younger son and those who, like the older son, pursue religious obedience in hopes of earning God\'s favor.

- Pray for both types of sons in this land to repent and to be restored to true relationship with their Heavenly Father.', 'ramadan-2026' ),
                __( 'Islam does not teach that God is a Father, nor that He gently goes after the lost, picking them up and placing them on His shoulders like the lost sheep described in this passage.

Ines suffered from bipolar disorder and had a difficult relationship with her husband. She had only known threats of God\'s judgment and punishment, not His gentle love. When she met Christ, she struggled to understand what she was experiencing. She said, "I feel an overwhelming amount of joy. But you know, I got used to being sad, I didn\'t even know how to live with joy anymore. I was confused, scared, and I always wanted to cry. Now, I feel so much joy—real joy from my heart. I don\'t know what it is that made God love me like this. He is present with me all the time, in every moment, in every second. I always feel Him telling me, \'You are safe. Do not be afraid.\'"

Pray for many in this land to experience the same joy described in Luke 15 – the joy of the lost son as the Father runs towards him – this same joy that Ines struggled to put into words.', 'ramadan-2026' ),
                __( '- Pray for Christians in this place to have their vision and heart expanded to grasp how deeply God loves the lost Muslims in this area. Pray that their hearts would continue to soften and that they would become more like their Father -- going after the lost sheep, sweeping the house in search for the coin, and running towards lost sons returning home.
- Pray for their hearts to be cleansed of any "older brother" tendencies that would rather see Muslims remain distant than be restored to the Father through repentance. Pray that believers in this land would rejoice and celebrate every time a lost sinner comes home.', 'ramadan-2026' ),
                __( 'Thank you God, for the way this story points us to our perfect heavenly Father and to our true, noble, and selfless Older Brother.

We pray for many in this land to live as described in Romans 8:12-17, according to the Spirit. "For all who are led by the Spirit of God are sons of God." We ask that You would free people here from the spirit of slavery to fall back into fear, and that they may receive the Spirit of adoption as sons, by whom they would cry, "Abba! Father."

Holy Spirit, please bear witness that many among this people are children of God. And if children, then heirs -- heirs of God and fellow heirs with Christ – provided we suffer with Him in order that we may also be glorified with Him.', 'ramadan-2026' ),
            ],
            //day 23
            [
                __( 'Day 23: Different Responses to God', 'ramadan-2026' ),
                __( 'Matthew 13:1-9
The same day went Jesus out of the house, and sat by the sea side. And great multitudes were gathered together unto him, so that he went into a ship, and sat; and the whole multitude stood on the shore. And he spake many things unto them in parables, saying, Behold, a sower went forth to sow; And when he sowed, some seeds fell by the way side, and the fowls came and devoured them up: Some fell upon stony places, where they had not much earth: and forthwith they sprung up, because they had no deepness of earth: And when the sun was up, they were scorched; and because they had no root, they withered away. And some fell among thorns; and the thorns sprung up, and choked them: But other fell into good ground, and brought forth fruit, some an hundredfold, some sixtyfold, some thirtyfold. Who hath ears to hear, let him hear.

Matthew 13:18-23
Hear ye therefore the parable of the sower. When any one heareth the word of the kingdom, and understandeth it not, then cometh the wicked one, and catcheth away that which was sown in his heart. This is he which received seed by the way side. But he that received the seed into stony places, the same is he that heareth the word, and anon with joy receiveth it; Yet hath he not root in himself, but dureth for a while: for when tribulation or persecution ariseth because of the word, by and by he is offended. He also that received seed among the thorns is he that heareth the word; and the care of this world, and the deceitfulness of riches, choke the word, and he becometh unfruitful. But he that received seed into the good ground is he that heareth the word, and understandeth it; which also beareth fruit, and bringeth forth, some an hundredfold, some sixty, some thirty.

Matthew 13:44-46
Again, the kingdom of heaven is like unto treasure hid in a field; the which when a man hath found, he hideth, and for joy thereof goeth and selleth all that he hath, and buyeth that field. Again, the kingdom of heaven is like unto a merchant man, seeking goodly pearls: Who, when he had found one pearl of great price, went and sold all that he had, and bought it.', 'ramadan-2026' ),
                __( 'God uses simple, ordinary things that everyone understands – seeds, soil, and planting – to communicate deep truths about His Kingdom.

- Pray for people in this land to have opportunities to learn about God\'s Kingdom in simple, culturally relevant ways.
- Pray that they would hear this story and examine the condition of their heart.
- Pray they would desire to have hearts like the fourth type of soil – hearts that not only hear and understand God\'s Word, but also go on to bear multiplicative fruit.', 'ramadan-2026' ),
                __( 'Human hearts are affected by internal and external factors -- persecution, the cares of the world, greed, concern over what people think, to name just a few.

- Pray for Muslims to have ears to hear and minds to understand when they encounter the Word of God.
- Pray for roots to grow deep into HIm so they might be sustained when persecution and tribulation come.
- Pray for hearts to rightly discern the fleeting pleasures of wealth and comfort and to pursue what will last forever.', 'ramadan-2026' ),
                __( 'Faten was a busy, divorced mother of four young adult and teenaged children. When her teenage daughter began to be curious about Christianity, Faten was open-minded to this interest and not stressed. Over time, her daughter\'s interest decreased, but Faten herself continued to pursue an understanding of Jesus.

As time passed, when she had opportunities to listen to the Bible, she would begin to fall asleep even while sitting upright – though she had once been alert and engaged. She liked what she heard about Jesus, but the concerns of the world and spiritual oppression she experienced as she tried to move towards the Light weighed heavily on her and dulled her interest.

Father, we don\'t understand all of Faten\'s story, but You do. We trust that her story is not finished. For every Muslim seeker in this land, by Your grace, please continue their story. May they be good soil that bears fruit thirty, sixty, and a hundredfold.', 'ramadan-2026' ),
                __( 'As Christians live among this people day by day, grow their conviction that the Kingdom of heaven is a treasure and a pearl of great price, worth giving up everything to attain.

- Pray for Muslims in this land to see that Christians\' true hope is not found in money, family, passports, possessions, or personalities. Their hope is that they found a treasure hidden in a field and in joy they sold all they had to buy the field.
- Pray that Christians would share this truth in ways that help Muslims understand that this treasure is available to them as well.', 'ramadan-2026' ),
                __( 'Father, we pray according to Philippians 3:8-14 that you would raise up men and women from this land who can say, "Whatever gain I had, I count it as loss for the sake of Christ." May they count everything as loss because of the surpassing worth of knowing Christ Jesus their Lord.

For His sake would they be able to say they\'ve suffered the loss of all things and count them as rubbish in order that they may gain Christ and be found in Him, not having a righteousness of their own that comes from the law, but that which comes through faith in Christ, the righteousness from God that depends on faith.

We pray that the people of this land would know Christ and the power of His resurrection, share in His sufferings, become like Him in His death, and by any means possible attain the resurrection from the dead.

May God raise up people in this place who would press on to make this their own, because Christ Jesus has made them His own. May they be able to say they forget what lies behind and strain forward to what lies ahead, pressing on toward the goal for the prize of the upward call of God in Christ Jesus.', 'ramadan-2026' ),
            ],
            //day 24
            [
                __( 'Day 24: Jesus\' Power Over Death', 'ramadan-2026' ),
                __( 'John 11:1-44
Now a certain man was sick, named Lazarus, of Bethany, the town of Mary and her sister Martha. (It was that Mary which anointed the Lord with ointment, and wiped his feet with her hair, whose brother Lazarus was sick.) Therefore his sisters sent unto him, saying, Lord, behold, he whom thou lovest is sick. When Jesus heard that, he said, This sickness is not unto death, but for the glory of God, that the Son of God might be glorified thereby. Now Jesus loved Martha, and her sister, and Lazarus. When he had heard therefore that he was sick, he abode two days still in the same place where he was. Then after that saith he to his disciples, Let us go into Judaea again. His disciples say unto him, Master, the Jews of late sought to stone thee; and goest thou thither again? Jesus answered, Are there not twelve hours in the day? If any man walk in the day, he stumbleth not, because he seeth the light of this world. But if a man walk in the night, he stumbleth, because there is no light in him. These things said he: and after that he saith unto them, Our friend Lazarus sleepeth; but I go, that I may awake him out of sleep. Then said his disciples, Lord, if he sleep, he shall do well. Howbeit Jesus spake of his death: but they thought that he had spoken of taking of rest in sleep. Then said Jesus unto them plainly, Lazarus is dead. And I am glad for your sakes that I was not there, to the intent ye may believe; nevertheless let us go unto him. Then said Thomas, which is called Didymus, unto his fellowdisciples, Let us also go, that we may die with him. Then when Jesus came, he found that he had lain in the grave four days already. Now Bethany was nigh unto Jerusalem, about fifteen furlongs off: And many of the Jews came to Martha and Mary, to comfort them concerning their brother. Then Martha, as soon as she heard that Jesus was coming, went and met him: but Mary sat still in the house. Then said Martha unto Jesus, Lord, if thou hadst been here, my brother had not died. But I know, that even now, whatsoever thou wilt ask of God, God will give it thee. Jesus saith unto her, Thy brother shall rise again. Martha saith unto him, I know that he shall rise again in the resurrection at the last day. Jesus said unto her, I am the resurrection, and the life: he that believeth in me, though he were dead, yet shall he live: And whosoever liveth and believeth in me shall never die. Believest thou this? She saith unto him, Yea, Lord: I believe that thou art the Christ, the Son of God, which should come into the world. And when she had so said, she went her way, and called Mary her sister secretly, saying, The Master is come, and calleth for thee. As soon as she heard that, she arose quickly, and came unto him. Now Jesus was not yet come into the town, but was in that place where Martha met him. The Jews then which were with her in the house, and comforted her, when they saw Mary, that she rose up hastily and went out, followed her, saying, She goeth unto the grave to weep there. Then when Mary was come where Jesus was, and saw him, she fell down at his feet, saying unto him, Lord, if thou hadst been here, my brother had not died. When Jesus therefore saw her weeping, and the Jews also weeping which came with her, he groaned in the spirit, and was troubled, And said, Where have ye laid him? They said unto him, Lord, come and see. Jesus wept. Then said the Jews, Behold how he loved him! And some of them said, Could not this man, which opened the eyes of the blind, have caused that even this man should not have died? Jesus therefore again groaning in himself cometh to the grave. It was a cave, and a stone lay upon it. Jesus said, Take ye away the stone. Martha, the sister of him that was dead, saith unto him, Lord, by this time he stinketh: for he hath been dead four days. Jesus saith unto her, Said I not unto thee, that, if thou wouldest believe, thou shouldest see the glory of God? Then they took away the stone from the place where the dead was laid. And Jesus lifted up his eyes, and said, Father, I thank thee that thou hast heard me. And I knew that thou hearest me always: but because of the people which stand by I said it, that they may believe that thou hast sent me. And when he thus had spoken, he cried with a loud voice, Lazarus, come forth. And he that was dead came forth, bound hand and foot with graveclothes: and his face was bound about with a napkin. Jesus saith unto them, Loose him, and let him go.', 'ramadan-2026' ),
                __( 'God is deeply moved by human suffering. Even though He knows the end from the beginning, He weeps over the pain His children experience in the temporal loss.

- Pray for people in this place to learn about this compassionate attribute of God. He is not far off, observing His children\'s suffering from a distance – He is near. Pray that His nearness would bring comfort and healing and ask for hearts that would be drawn into a transforming relationship with Him.
- Pray for Muslims to discover the truth of Jesus\' promise in John 11:25, "I am the resurrection and the life. Whoever believes in me, though he die, yet shall he live".', 'ramadan-2026' ),
                __( 'This passage teaches us that death is crushingly difficult for humanity to accept. Deep down, we know that death is not right and that it is not a part of God\'s original design.

- Pray for Muslims mourning the loss of a loved one today to know that grief is meant to be processed, not suppressed.
- Pray that they would hear and understand that death was never God\'s design and that He ultimately defeated death at the cross.', 'ramadan-2026' ),
                __( 'Every human knows the breathtaking pain that comes when a loved one dies. Often, grief is compounded by regret over harsh words spoken, evil actions taken, or missed opportunities that can never be recovered. Among Muslims, expressions of grief vary from culture to culture. Some cling robotically to repeated phrases of praise to God, reminding themselves of His mercy. Others express grief through acts they consider righteous, hoping to improve the deceased person\'s chances in the afterlife.

- Pray that this pain and grief would move Muslims in this place to seek deeper and more certain answers.
- Pray that they would encounter Christians who can share the unshakeable hope and assurance found in Jesus\' victory over death. May they find joy in learning that those who belong to Christ will share in His resurrection and will never die again.', 'ramadan-2026' ),
                __( 'Rahma became a Christian as a young adult and soon faced intense persecution from her family. Her Bible was confiscated, and her brother beat her. A decade later, when her father was dying, she returned home to be with him. One day, she prayed over him and shared much of the Gospel message in that prayer. Though he drifted in and out of consciousness, he woke long enough to say, "I\'ve heard this before." Rahma later discovered that he had secretly kept her confiscated Bible, read it, and treasured it deeply. Eventually he died.

As the family prepared his body and began the custom Muslim burial rituals, something extraordinary happened: a short time later, he rose and removed the shroud from his own face. In the weeks that followed, Rahma confirmed that her father placed his faith in Jesus.

Like Lazarus, Rahma\'s father died a second time. Yet, also like Lazarus, because of his union with Christ, he had the sure hope of eternal life. Pray for Christians in this place to boldly share the hope they have in Christ and for their lives to reflect they are not enslaved by the fear of death. They know Christ\'s victory – and it changed everything, including how they live now.', 'ramadan-2026' ),
                __( 'Father, we thank you for Jesus, who shared in our flesh and blood so that through death He might destroy the one who has the power of death, that is, the devil, and deliver all those who through fear of death were subject to lifelong slavery. (Hebrews 2:14-15)

Free men and women all over this land from the slavery of the fear of death.

Along with followers of Jesus from all over the world, may many in this place join in singing \'Death is swallowed up in victory. O death, where is your sting?" Thanks be to God, who gives us the victory through our Lord Jesus Christ. Therefore, we pray for brothers and sisters in this place to be steadfast, immovable, always abounding in the work of the Lord, knowing that in the Lord their labor is not in vain. (1 Corinthians 15:55-58)', 'ramadan-2026' ),
            ],
            //day 25
            [
                __( 'Day 25: Jesus\' Humility', 'ramadan-2026' ),
                __( 'John 13:1-17
Now before the feast of the passover, when Jesus knew that his hour was come that he should depart out of this world unto the Father, having loved his own which were in the world, he loved them unto the end. And supper being ended, the devil having now put into the heart of Judas Iscariot, Simon\\\'s son, to betray him; Jesus knowing that the Father had given all things into his hands, and that he was come from God, and went to God; He riseth from supper, and laid aside his garments; and took a towel, and girded himself. After that he poureth water into a bason, and began to wash the disciples\\\' feet, and to wipe them with the towel wherewith he was girded. Then cometh he to Simon Peter: and Peter saith unto him, Lord, dost thou wash my feet? Jesus answered and said unto him, What I do thou knowest not now; but thou shalt know hereafter. Peter saith unto him, Thou shalt never wash my feet. Jesus answered him, If I wash thee not, thou hast no part with me. Simon Peter saith unto him, Lord, not my feet only, but also my hands and my head. Jesus saith to him, He that is washed needeth not save to wash his feet, but is clean every whit: and ye are clean, but not all. For he knew who should betray him; therefore said he, Ye are not all clean. So after he had washed their feet, and had taken his garments, and was set down again, he said unto them, Know ye what I have done to you? Ye call me Master and Lord: and ye say well; for so I am. If I then, your Lord and Master, have washed your feet; ye also ought to wash one another\\\'s feet. For I have given you an example, that ye should do as I have done to you. Verily, verily, I say unto you, The servant is not greater than his lord; neither he that is sent greater than he that sent him. If ye know these things, happy are ye if ye do them.', 'ramadan-2026' ),
                __( 'God does things that we only understand later: "What I am doing you do not understand now, but afterward you will understand."

- Pray for the message of the upside-down Kingdom -- where the last are first, the poor are blessed, and the greatest is the servant of all -- to be seen for what it truly is: a message from God, not from man. Islam teaches victory through power and the sword; reward is often associated with authority and earthly pleasure. The way of Jesus is through humility, self-sacrifice, and death.
- Pray that when God allows pain and suffering in the lives of His children in this place, it will drive them deeper into relationship with Him and grow their trust in Him.', 'ramadan-2026' ),
                __( 'One weakness of humanity is thinking that our judgments are better than God\'s regarding what is or is not appropriate for either Him or us.

- Pray for people in this place to repent of imposing their expectations of who God should be and what they are entitled to. Instead, may they humbly approach God on His terms and with an open heart.
- Pray for soft hearts that would let Him shape them into His image, rather than trying to shape Him into their image.', 'ramadan-2026' ),
                __( 'Islam has no teaching, understanding, or capacity for an image of a God who stoops down to wash the feet of His followers. This teaching is shocking, repulsive, and offensive to them -- just as it was to the Jews of Jesus\' day. Jesus persevered with them and continued to reshape their understanding, eventually giving them His Spirit, who could transform them from the inside out to see things the way He does.

- Pray for people in this place to encounter Jesus and for God\'s grace to enable them to receive this message of humble service.', 'ramadan-2026' ),
                __( 'Two Christian families who stepped away from good government jobs in their home country, chose to serve in a 99% Muslim nation as church planters five years ago. When a coup caused their financial support to drop to nothing, the organization asked them to return home.

Still feeling God\'s call, they sought to return to the Muslim nation without resources. Once there, they embraced the local lifestyle entirely, working in the gardens and farms just like the local people. This humble example was revolutionary. Traditionally, Muslim women in the community stayed home, but seeing these Christian wives working in the fields inspired other women to join them.

They started Discovery Bible Study (DBS) groups, teaching women about healthy diets and how to support their families by sharing and working the land together. This work brought dignity and joy. The men eventually joined the farm work too. Despite floods sometimes sweeping away everything, the couples remain obedient and courageous, continuing to share the gospel. They are making disciples, baptizing people, and seeing Christ manifested in the community because they truly made themselves of "no reputation."

Pray for Christians in this place to model the humility of Christ and that it would be a compelling witness, drawing families to follow Jesus.', 'ramadan-2026' ),
                __( 'We intercede for our brothers and sisters in Christ in this place, praying Philippians 2 over them. May they be of the same mind, having the same love. We pray they would do nothing from selfish ambition or conceit, but in humility count others more significant than themselves.

As they look not only to their own interests, but also to the interest of others, may it be a compelling testimony that would make nonbelieving onlookers want to know more about the source of the power to put others first.

We pray our brothers and sisters here would have the mind of Christ, who though He was in the form of God, did not count equality with God a thing to be grasped, but emptied Himself, by taking the form of a servant, being born in the likeness of men.', 'ramadan-2026' ),
            ],
            //day 26
            [
                __( 'Day 26: Passover with the Disciples', 'ramadan-2026' ),
                __( 'Matthew 26:17-30
Now the first day of the feast of unleavened bread the disciples came to Jesus, saying unto him, Where wilt thou that we prepare for thee to eat the passover? And he said, Go into the city to such a man, and say unto him, The Master saith, My time is at hand; I will keep the passover at thy house with my disciples. And the disciples did as Jesus had appointed them; and they made ready the passover. Now when the even was come, he sat down with the twelve. And as they did eat, he said, Verily I say unto you, that one of you shall betray me. And they were exceeding sorrowful, and began every one of them to say unto him, Lord, is it I? And he answered and said, He that dippeth his hand with me in the dish, the same shall betray me. The Son of man goeth as it is written of him: but woe unto that man by whom the Son of man is betrayed! it had been good for that man if he had not been born. Then Judas, which betrayed him, answered and said, Master, is it I? He said unto him, Thou hast said. And as they were eating, Jesus took bread, and blessed it, and brake it, and gave it to the disciples, and said, Take, eat; this is my body. And he took the cup, and gave thanks, and gave it to them, saying, Drink ye all of it; For this is my blood of the new testament, which is shed for many for the remission of sins. But I say unto you, I will not drink henceforth of this fruit of the vine, until that day when I drink it new with you in my Father\\\'s kingdom. And when they had sung an hymn, they went out into the mount of Olives.', 'ramadan-2026' ),
                __( 'God is writing one unified story. We began tracing this story on Day 1 of this prayer campaign with God speaking creation into being. It continued with the promise about the Seed of the woman on Day 3, Isaac\'s substitutionary sacrifice on Day 8, and the doorways painted with the lamb\'s blood on Day 10. All of this builds up to today\'s passage, the fulfillment of the new covenant that Jesus established.

- Pray today for families and groups of friends who have newly discovered God\'s Word. Pray that they would have the perseverance and hunger to continue discovering Scripture until they get far enough along to see the beauty of the story fully emerge.', 'ramadan-2026' ),
                __( 'All of humanity is capable of betrayal. When Jesus said that one of his followers would betray him, they all became sorrowful and asked one another, "Is it I?" Pray for people in this land to have honest self-awareness about their capacity to do evil. Pray that this revelation would drive them to the only one who can forgive them of the evil they have done and give them a new heart -- holy, pure, selfless, and loving.', 'ramadan-2026' ),
                __( 'Radhia grew up in a conservative Muslim family. She led a normal life, marrying, having children, and being a good neighbor. When she inexplicably lost more than 20 pounds, she went to a doctor to seek answers. She discovered she had a very rare intestinal disorder and that she would need an expensive surgery. She reached out to her family to help her, but found that no one was willing to support her. Confused and feeling abandoned, she reached out to her neighbor who was a Christian. Despite this neighbor having even more difficult financial circumstances than Radhia, by God\'s grace this Christian was able to help her and walk with her through her overwhelmingly difficult circumstances.

Which of us has not experienced betrayal? Muslims are not an exception. Sadly, they do not know a Lord who knowingly accepted betrayal and continued to love his betrayer until the last moment.

Pray that when Muslims in this place experience human betrayal, they would have an opportunity through Christ and/or His followers to experience undeserved acceptance, love, and forgiveness.

Miraculously, while Radhia awaited surgery she put her trust in Jesus and he healed her. The surgery was canceled and Radhia\'s family is now trying to understand what happened. Pray for many hearts to be drawn to Christ through this testimony.', 'ramadan-2026' ),
                __( '- Pray for Christians in this area to receive encouragement and a refocusing of their faith each time they share in the Lord\'s Supper. Pray that it strengthens their resolve and leads to deeper fellowship with Jesus.
- Pray for believers living in isolation. Pray that God would raise up believers from among their friends and family, so they would experience the blessing of being a part of Christ\'s body. Pray that they would be sensitive and obedient to the ways God wants to use them to reach those around them. Pray for fellowship for every Christian. May they have brothers and sisters with whom they can share the Lord\'s Supper.', 'ramadan-2026' ),
                __( 'On the night Jesus was betrayed, he said, "This is my body, which is for you. Do this in remembrance of me." In the same way also he took the cup, after supper, saying, "This cup is the new covenant in my blood. Do this, as often as you drink it, in remembrance of me." For as often as you eat this bread and drink the cup, you proclaim the Lord\'s death until he comes." (1 Corinthians 11:23-26)

Pray that every time brothers and sisters take communion – eating the bread and drinking the cup – they would proclaim the Lord\'s death. Pray this bold proclamation would lead to multitudes of people in this land becoming followers of Christ who participate in communion. Pray that the practice of taking the Lord\'s Supper would be an act of spiritual defiance against the spiritual forces that try to say, "Jesus didn\'t really die." May it be taken with eager expectation for the day we will drink it again with Jesus in His Father\'s Kingdom.', 'ramadan-2026' ),
            ],
            //day 27
            [
                __( 'Day 27: Jesus Arrested', 'ramadan-2026' ),
                __( 'Matthew 26:36-56
Then cometh Jesus with them unto a place called Gethsemane, and saith unto the disciples, Sit ye here, while I go and pray yonder. And he took with him Peter and the two sons of Zebedee, and began to be sorrowful and very heavy. Then saith he unto them, My soul is exceeding sorrowful, even unto death: tarry ye here, and watch with me. And he went a little further, and fell on his face, and prayed, saying, O my Father, if it be possible, let this cup pass from me: nevertheless not as I will, but as thou wilt. And he cometh unto the disciples, and findeth them asleep, and saith unto Peter, What, could ye not watch with me one hour? Watch and pray, that ye enter not into temptation: the spirit indeed is willing, but the flesh is weak. He went away again the second time, and prayed, saying, O my Father, if this cup may not pass away from me, except I drink it, thy will be done. And he came and found them asleep again: for their eyes were heavy. And he left them, and went away again, and prayed the third time, saying the same words. Then cometh he to his disciples, and saith unto them, Sleep on now, and take your rest: behold, the hour is at hand, and the Son of man is betrayed into the hands of sinners. Rise, let us be going: behold, he is at hand that doth betray me. And while he yet spake, lo, Judas, one of the twelve, came, and with him a great multitude with swords and staves, from the chief priests and elders of the people. Now he that betrayed him gave them a sign, saying, Whomsoever I shall kiss, that same is he: hold him fast. And forthwith he came to Jesus, and said, Hail, master; and kissed him. And Jesus said unto him, Friend, wherefore art thou come? Then came they, and laid hands on Jesus, and took him. And, behold, one of them which were with Jesus stretched out his hand, and drew his sword, and struck a servant of the high priest\\\'s, and smote off his ear. Then said Jesus unto him, Put up again thy sword into his place: for all they that take the sword shall perish with the sword. Thinkest thou that I cannot now pray to my Father, and he shall presently give me more than twelve legions of angels? But how then shall the scriptures be fulfilled, that thus it must be? In that same hour said Jesus to the multitudes, Are ye come out as against a thief with swords and staves for to take me? I sat daily with you teaching in the temple, and ye laid no hold on me. But all this was done, that the scriptures of the prophets might be fulfilled. Then all the disciples forsook him, and fled.', 'ramadan-2026' ),
                __( 'Jesus was completely capable of stopping his arrest and subsequent crucifixion. He submitted willingly, not because of powerlessness, but because of obedience to His Father\'s will. Multiple times he prayed "Not my will, but your will be done." Pray for the beauty of Jesus\' willing sacrifice to bring many in this land to love and follow Him.

We also learn that Jesus experienced the deepest sorrow known to humanity. He was not stoic or detached from suffering. Pray for those in this land who are in deep grief to know that Jesus experienced even greater sorrow and desires to be with them in their pain.', 'ramadan-2026' ),
                __( 'Humans are weak. Our bodies get tired. Peter was full of zeal and claimed that he was willing to go anywhere with Jesus no matter the cost. Yet, after the Messiah asked him to watch and pray, he could not even keep his eyes open. Pray that this example would convince Muslims in this place that they cannot fast enough, pray enough, give away enough. Pray for this conviction to drive them to Jesus\' grace.

None of us has been a friend to Jesus like he has been to us. Pray for many in this land to experience friendship with Jesus, allowing that to transform them into His image more and more each day.', 'ramadan-2026' ),
                __( 'There is only one way for us to be rescued from this body of death. God the Son cried out to His Father, "If there is another way, take this cup from me." God the Father did not take it away. Only by drinking this cup of suffering was eternal life made available to all who would believe.

Halima had heard the stories of Jesus. She asked for them to be told to her over and over again. She loved the stories and explained Jesus\' power by declaring that He is God. But as her family came to visit and her mother fasted and her father went to prayer times, she could not reconcile the outward "goodness" of family members who are Muslim with the gospel that Jesus is the only way. She couldn\'t bear to believe that God would not overlook their unbelief in Jesus because of their devout sincerity. She was sure that God would see their "good lives", forgive them, and grant them eternal life. She began saying her Muslim prayers more devoutly, counting them with beads, fasting twice a week, and was careful to dress modestly. She said, "It is just clear what is good and what is bad. You can feel it."

Like many others, Halima believed the lie that we define the way to God – that good and evil are determined by how something feels to us.

How, then, can Christians be certain of eternal life? God himself declares that there is One Way to the Father – through Jesus the Son. Pray for humility and repentance.', 'ramadan-2026' ),
                __( 'When believers suffer for Christ\'s sake, their families are often unsure how to pray. If we know that "blessed are those who are persecuted for righteousness\' sake," is it right to pray for their release from prison? If God works powerfully even in prison, should we grieve and beg for our family to be reunited?

This passage reassures us that in our anguish, we may cry out to God in holiness, "If it is possible, take this cup from me." At the same time we pray, "Not our will, God, but Yours be done." God advances the gospel. God builds His church. We trust Him even when we cannot see.

Through tears, we cry honestly and desperately to God, trusting the perfect sovereignty of the One who hears and who loves.

- Pray for Christians in this place who are facing deep sorrows, troubles, and persecution to take comfort in knowing that Jesus sees and understands.
- The Muslim world still waits for multitudes of Christians who will respond to God\'s call in obedient action, not merely philosophical agreement. Pray that believers would follow the way of their Master – even when obedience leads through sorrow – to bring the Good News to Muslims.', 'ramadan-2026' ),
                __( 'Blessed be the God and Father of our Lord Jesus Christ, the Father of mercies and God of all comfort, who comforts His people in all their affliction, so that they may comfort others with the same comfort they received from God. As believers in this land share abundantly in Christ\'s sufferings, may they also share in His comfort. (2 Corinthians 1:3-5)

Pray for people in this place, like Paul, to testify in the midst of crushing hardship: "This was to make us rely not on ourselves but on God who raises the dead." (2 Corinthians 1:9)', 'ramadan-2026' ),
            ],
            //day 28
            [
                __( 'Day 28: The Crucifixion', 'ramadan-2026' ),
                __( 'Luke 23:32-56
And there were also two other, malefactors, led with him to be put to death. And when they were come to the place, which is called Calvary, there they crucified him, and the malefactors, one on the right hand, and the other on the left. Then said Jesus, Father, forgive them; for they know not what they do. And they parted his raiment, and cast lots. And the people stood beholding. And the rulers also with them derided him, saying, He saved others; let him save himself, if he be Christ, the chosen of God. And the soldiers also mocked him, coming to him, and offering him vinegar, And saying, If thou be the king of the Jews, save thyself. And a superscription also was written over him in letters of Greek, and Latin, and Hebrew, THIS IS THE KING OF THE JEWS. And one of the malefactors which were hanged railed on him, saying, If thou be Christ, save thyself and us. But the other answering rebuked him, saying, Dost not thou fear God, seeing thou art in the same condemnation? And we indeed justly; for we receive the due reward of our deeds: but this man hath done nothing amiss. And he said unto Jesus, Lord, remember me when thou comest into thy kingdom. And Jesus said unto him, Verily I say unto thee, To day shalt thou be with me in paradise. And it was about the sixth hour, and there was a darkness over all the earth until the ninth hour. And the sun was darkened, and the veil of the temple was rent in the midst. And when Jesus had cried with a loud voice, he said, Father, into thy hands I commend my spirit: and having said thus, he gave up the ghost. Now when the centurion saw what was done, he glorified God, saying, Certainly this was a righteous man. And all the people that came together to that sight, beholding the things which were done, smote their breasts, and returned. And all his acquaintance, and the women that followed him from Galilee, stood afar off, beholding these things. And, behold, there was a man named Joseph, a counsellor; and he was a good man, and a just: (The same had not consented to the counsel and deed of them;) he was of Arimathaea, a city of the Jews: who also himself waited for the kingdom of God. This man went unto Pilate, and begged the body of Jesus. And he took it down, and wrapped it in linen, and laid it in a sepulchre that was hewn in stone, wherein never man before was laid. And that day was the preparation, and the sabbath drew on. And the women also, which came with him from Galilee, followed after, and beheld the sepulchre, and how his body was laid. And they returned, and prepared spices and ointments; and rested the sabbath day according to the commandment.', 'ramadan-2026' ),
                __( 'From the cross, Jesus showed compassion to others\' agony and pain while He Himself was suffering unimaginably during His unjust death. He comforted the repentant criminal and forgave those who tortured and ridiculed Him.

- Pray for people in this place to have eyes that see the contrast between Jesus\' heart attitude and actions and those of political and religious leaders throughout human history. Ask that Jesus\' model would not only be respected in this land, but that His actions would be emulated.
- Pray that Jesus\' acts of forgiveness would influence those struggling with shame over the wrong actions they have committed. May they come confidently to Him to receive full pardon for their sins. May the freedom that comes from this forgiveness result in ever-increasing love for Him.', 'ramadan-2026' ),
                __( 'When humanity sees Jesus\' actions and character, some respond in faith, while others respond with rejection and ridicule. Pray for many in this place to be the first, not the latter. May His character and actions, as recorded in the Bible, also be reflected in the lives of His followers, and may they be a testimony to Muslim seekers.', 'ramadan-2026' ),
                __( 'Muslims esteem Jesus as a prophet and do not believe God would allow Him to be killed in such a humiliating manner. Despite disputing theories common throughout Islam, the eye accounts and history confirm Jesus\' death.

- Pray for those reading this story to understand how Jesus\' death was essential to God\'s plan for the salvation of mankind.
- May His innocence, His submission, and the miracles that accompanied His death confirm for them that Jesus was not just a prophet. He was exactly who He claimed to be: the Son of God.', 'ramadan-2026' ),
                __( 'Imen is living abroad to make money to send to her family. While in her adopted country she came to Christ through reading Discovery Bible Study (DBS)\'s Creation to Christ series from the Bible. She immediately began sharing and leading other Muslims to Christ. Early in her journey she shared with her friend Samar. Initially Samar liked what she was hearing, but soon she became distracted by life\'s concerns. Sometime later, Imen was studying the Crucifixion story. As she was reading, "By this time it was about noon, and darkness fell across the whole land…" she received a text from Samar saying Samar\'s sister had died. Samar had just moved back to her home country to support her sister\'s dialysis treatments. Tragically the power went out while her sister was having dialysis and that power outage killed her. In response, Imen said somberly, "God holds time in his hands. No one knows the time or the hour, or when the darkness will fall. We have to get right with Him."

We do not know the end of our days here on earth. Pray that Muslims here would not put off until tomorrow what should be done today. May they have a sense of urgency to seek the Lord, that He might forgive their sins and heal their land.', 'ramadan-2026' ),
                __( 'Hebrews 13:12-21 points out that Jesus suffered outside the gate in order to sanctify the people through his own blood. May believers in this land go to Him outside the camp and bear the reproach He endured. Help people in this land know that we have no lasting city here on earth, but may they seek the city that is to come. Through Christ may many in this land continually offer up a sacrifice of praise to God, that is the fruit of lips that acknowledge His name.

Believers here should not neglect to do good and to share what they have, most importantly the good news of Jesus, for such sacrifices are pleasing to You, God. Now may the God of peace who brought again from the dead our Lord Jesus, the great Shepherd of the sheep, by the blood of the eternal covenant, equip people here with everything good that they may do His will, working in them that which is pleasing in His sight, through Jesus Christ, to whom be glory forever and ever. Amen', 'ramadan-2026' ),
            ],
            //day 29
            [
                __( 'Day 29: The Resurrection', 'ramadan-2026' ),
                __( 'John 20:11-31
But Mary stood without at the sepulchre weeping: and as she wept, she stooped down, and looked into the sepulchre, And seeth two angels in white sitting, the one at the head, and the other at the feet, where the body of Jesus had lain. And they say unto her, Woman, why weepest thou? She saith unto them, Because they have taken away my Lord, and I know not where they have laid him. And when she had thus said, she turned herself back, and saw Jesus standing, and knew not that it was Jesus. Jesus saith unto her, Woman, why weepest thou? whom seekest thou? She, supposing him to be the gardener, saith unto him, Sir, if thou have borne him hence, tell me where thou hast laid him, and I will take him away. Jesus saith unto her, Mary. She turned herself, and saith unto him, Rabboni; which is to say, Master. Jesus saith unto her, Touch me not; for I am not yet ascended to my Father: but go to my brethren, and say unto them, I ascend unto my Father, and your Father; and to my God, and your God. Mary Magdalene came and told the disciples that she had seen the Lord, and that he had spoken these things unto her. Then the same day at evening, being the first day of the week, when the doors were shut where the disciples were assembled for fear of the Jews, came Jesus and stood in the midst, and saith unto them, Peace be unto you. And when he had so said, he shewed unto them his hands and his side. Then were the disciples glad, when they saw the Lord. Then said Jesus to them again, Peace be unto you: as my Father hath sent me, even so send I you. And when he had said this, he breathed on them, and saith unto them, Receive ye the Holy Ghost: Whose soever sins ye remit, they are remitted unto them; and whose soever sins ye retain, they are retained. But Thomas, one of the twelve, called Didymus, was not with them when Jesus came. The other disciples therefore said unto him, We have seen the Lord. But he said unto them, Except I shall see in his hands the print of the nails, and put my finger into the print of the nails, and thrust my hand into his side, I will not believe. And after eight days again his disciples were within, and Thomas with them: then came Jesus, the doors being shut, and stood in the midst, and said, Peace be unto you. Then saith he to Thomas, Reach hither thy finger, and behold my hands; and reach hither thy hand, and thrust it into my side: and be not faithless, but believing. And Thomas answered and said unto him, My Lord and my God. Jesus saith unto him, Thomas, because thou hast seen me, thou hast believed: blessed are they that have not seen, and yet have believed. And many other signs truly did Jesus in the presence of his disciples, which are not written in this book: But these are written, that ye might believe that Jesus is the Christ, the Son of God; and that believing ye might have life through his name.

Luke 24:50-52
And he led them out as far as to Bethany, and he lifted up his hands, and blessed them. And it came to pass, while he blessed them, he was parted from them, and carried up into heaven. And they worshipped him, and returned to Jerusalem with great joy.', 'ramadan-2026' ),
                __( '- God raised Jesus from the dead just as He promised. Pray for Muslims in to be counted among those who have not seen and yet have believed in the resurrection of Jesus.
- Who was the first eye witness of Jesus\' resurrection? It was Mary. Her role in this story honors and elevates a woman in a culture where a woman\'s testimony in court would not have been considered meaningful. Pray for women in this land to be led to faith in Christ through knowing how deeply they are loved and valued by God.', 'ramadan-2026' ),
                __( 'The response of the disciples in this passage highlights how humanity often reacts in the face of danger. The disciples were locked in a room together out of fear. Jesus came to them and repeatedly said, "Peace be with you." Pray that despite the fear that keeps Muslims from asking questions about their religion and discovering what the Bible says, they would sense the peace of Jesus inviting them to follow Him.', 'ramadan-2026' ),
                __( 'Pray for the purpose of John\'s book, stated in verses 30 and 31, to be realized among Muslims in this place. "Now Jesus did many other signs in the presence of the disciples, which are not written in this book; but these are written so that you may believe that Jesus is the Christ, the Son of God, and that by believing you have life in his name."

Pray for Muslims to have both the opportunity and the desire to read this account and that it would cause them to believe that Jesus is the Christ, the Son of God. Thank God for the many Muslims who come to know Jesus through miracles, but pray for multitudes who have not seen, and yet will still believe.', 'ramadan-2026' ),
                __( 'When Saidia was a little girl she heard the words of Jesus on a radio broadcast. She loved what she heard and so she asked for a Bible to be mailed to her home. Her father received the Bible in the mail. Fearful that she would get in big trouble, she said nothing about where it came from, and her father kept it. Years passed and she continued to be interested in learning about Jesus. When her husband left her for another woman, though, she became desperate for Him. She cried out to Him and He came to her in a dream, filling the room with light. He called her to follow Him. One can imagine Him saying, "Saidia," just as He did to Mary.

Pray for many Muslims in this place to be visited by God and to choose to follow Jesus. Pray they would be like Mary – people who go and announce to their friends, "I have seen the Lord."', 'ramadan-2026' ),
                __( 'We pray according to 1 Peter 1:8-9 for our brothers and sisters in this land. Though they have not seen Jesus with their physical eyes, may they love him. Though they do not now see Him, they believe in Him. May they increasingly rejoice with joy that is inexpressible and filled with glory, obtaining the outcome of their faith, the salvation of their souls.

1 Corinthians 15:17, 19 and 22 remind us that if Christ has not been raised, our faith is futile and we remain in our sins. If in Christ we have hope in this life only, we are of all people most to be pitied. But we rejoice in the truth that Christ has been raised from the dead. Strengthen our brothers and sisters here to take hope in this. Capture the hearts of Muslims with the truth of the resurrection, and that in Christ, all shall be made alive.', 'ramadan-2026' ),
            ],
            //day 30
            [
                __( 'Day 30: Jesus in Heaven', 'ramadan-2026' ),
                __( 'Revelation 7:9-17
After this I beheld, and, lo, a great multitude, which no man could number, of all nations, and kindreds, and people, and tongues, stood before the throne, and before the Lamb, clothed with white robes, and palms in their hands; And cried with a loud voice, saying, Salvation to our God which sitteth upon the throne, and unto the Lamb. And all the angels stood round about the throne, and about the elders and the four beasts, and fell before the throne on their faces, and worshipped God, Saying, Amen: Blessing, and glory, and wisdom, and thanksgiving, and honour, and power, and might, be unto our God for ever and ever. Amen. And one of the elders answered, saying unto me, What are these which are arrayed in white robes? and whence came they? And I said unto him, Sir, thou knowest. And he said to me, These are they which came out of great tribulation, and have washed their robes, and made them white in the blood of the Lamb. Therefore are they before the throne of God, and serve him day and night in his temple: and he that sitteth on the throne shall dwell among them. They shall hunger no more, neither thirst any more; neither shall the sun light on them, nor any heat. For the Lamb which is in the midst of the throne shall feed them, and shall lead them unto living fountains of waters: and God shall wipe away all tears from their eyes.', 'ramadan-2026' ),
                __( 'God\'s love is so great that it encompasses multitudes from every tribe, tongue, and nation. One day all believers will be in His presence, delighting in Him without condemnation.

- Pray for Muslims in this place to put their faith in Christ and be among them.
- May Christians from a Muslim background take the Good News of Jesus to other Muslims from neighboring people groups.
- This passage also teaches that it is right to ascribe blessings, glory, wisdom, thanksgiving, honor, power, and might to God for all eternity, literally. Pray for God to raise up more people in this land who would begin living this reality even now.', 'ramadan-2026' ),
                __( 'Human hearts long for completion. We often look to tangible things like money, success, sex, relationships, food, or drink to meet this need. Our satisfaction in these things does not last.

- Pray that Muslims in this place will realize that they will find full satisfaction only in the presence of our Creator. Pray that they will taste this completion through a relationship with Jesus and the indwelling of the Holy Spirit provided for us here and now.
- May the promise of God dwelling with His people, pictured in this passage, provide hope for the fulfillment of this longing.', 'ramadan-2026' ),
                __( 'Muslims hope that their good deeds will outweigh their bad deeds. They believe they have an angel on each shoulder recording their actions. This passage shows us that God has a superior plan. His plan is not merely to make those who put their faith in Him have more good than bad. His plan is to make us pure, so that we can be in His holy, perfect presence.

- Pray for Muslims in this place to long for true purity and to seek a way for that to be possible.
- Pray that they would thirst for the springs of living water and meet a believer who can lead them to the one who promised if you drink of Him you will never thirst again.', 'ramadan-2026' ),
                __( 'Sonia grew up in a very poor family. She often went without her basic needs being met. Even more devastating, her need to be unconditionally loved by her mom was unmet. As a result, she made some painful decisions in relationships with men, leading to more disappointment, shame, and rejection. She came to know Jesus, sharing His love with others, but the wounds from her childhood have not yet been fully healed.

- Pray Sonia and every believer in this place will be healed from emotional trauma. Ask that God would meet them in the midst of their hurt and suffering now and that He would guide them to springs of living water on this side of eternity.
- Pray that He would fill them with assured hope and that He will wipe away every tear from their eyes.', 'ramadan-2026' ),
                __( 'May the Lamb receive the reward of His suffering in this land. May this Ramadan mark a turning point where many would take steps towards Christ -- whether it be reading the Bible, talking to a Christian, or humbly praying to God and simply asking Him to reveal himself to them. We pray for families and groups of friends to gather around the Bible to hear the stories we\'ve prayed through this month.

May the living and active Word of God, sharper than any double-edged sword, penetrate them and make them whole. We pray for those in this land to experience God dwelling with them as described in Revelation 21:3-5. Through Christ may they find hope that He will wipe away every tear from their eyes, and death shall be no more. Neither shall there be mourning, nor crying, nor anymore pain, for the former things have passed away. We ask in faith that those we have prayed for this year would say goodbye to the former things and would walk with the One who says, "Behold, I am making all things new."', 'ramadan-2026' ),
            ],
        ];

        $content = [];
        foreach ( $data as $index => $d ){

            $number = $index +1;
            if ( $number < 10 ){
                $number = '0' . $number;
            }

//            $image = '';
//            if ( file_exists( Ramadan_2026::$plugin_dir . 'images/' . $number . '.jpg' ) ) {
//                $image = '<figure class="wp-block-image p4m_prayer_image"><img src="' . plugins_url( 'images/' . $number . '.jpg', __DIR__ ) . '" alt="' . $number . '"  /></figure >';
//            }

            $content[] = [
                'excerpt' => wp_kses_post( self::ramadan_format_message( $d[0], $fields ) ),
                'content' => [
                    '<!-- wp:heading {"level":2} -->',
                    '<h2><strong>' . wp_kses_post( self::ramadan_format_message( $d[0], $fields ) ) . '</strong></h2>',
                    '<!-- /wp:heading -->',

                    '<!-- wp:paragraph -->',
                    '<p><em>' . __( 'One of the primary ways God is drawing Muslims to Himself is through simple Bible studies that highlight passages of scripture from Creation through the Resurrection. <a href="https://waha.app/">The Waha app</a> contains many story sets that have been translated into dozens of languages. Throughout the 30 days we will pray through these story sets that reveal Jesus, the Word made flesh.', 'ramadan-2026' ) . '</em></p>',
                    '<!-- /wp:paragraph -->',

                    '<!-- wp:paragraph -->',
                    '<p><em>' . __( 'As you read these familiar passages, prayerfully imagine what it would be like to read them for the first time – to see the wisdom, power, beauty, and authority of God and to be drawn to trust in Christ and yield your allegiance to Him.', 'ramadan-2026' ) . '</em></p>',
                    '<!-- /wp:paragraph -->',

                    '<!-- wp:heading {"level":3} -->',
                    '<h3><strong>' . __( 'Pray for your people and place according to what God shows you for today\'s scripture:', 'ramadan-2026' ) . '</strong></h3>',
                    '<!-- /wp:heading -->',

                    self::format_scripture_passages( $d[1], $fields ),

                    '<!-- wp:heading {"level":3} -->',
                    '<h3><strong>' . __( 'What this passage teaches us about God', 'ramadan-2026' ) . '</strong></h3>',
                    '<!-- /wp:heading -->',

                    '<!-- wp:paragraph -->',
                    '<p>' . wp_kses_post( self::ramadan_format_message( $d[2], $fields ) ) . '</p>',
                    '<!-- /wp:paragraph -->',

                    '<!-- wp:heading {"level":3} -->',
                    '<h3><strong>' . __( 'What this passage teaches us about humanity', 'ramadan-2026' ) . '</strong></h3>',
                    '<!-- /wp:heading -->',

                    '<!-- wp:paragraph -->',
                    '<p>' . wp_kses_post( self::ramadan_format_message( $d[3], $fields ) ) . '</p>',
                    '<!-- /wp:paragraph -->',

                    '<!-- wp:heading {"level":3} -->',
                    '<h3><strong>' . __( 'Insight into how this passage connects to Muslims', 'ramadan-2026' ) . '</strong></h3>',
                    '<!-- /wp:heading -->',

                    '<!-- wp:paragraph -->',
                    '<p>' . wp_kses_post( self::ramadan_format_message( $d[4], $fields ) ) . '</p>',
                    '<!-- /wp:paragraph -->',

                    '<!-- wp:heading {"level":3} -->',
                    '<h3><strong>' . __( 'Insight into how to pray this passage for Christians reaching Muslims in this location', 'ramadan-2026' ) . '</strong></h3>',
                    '<!-- /wp:heading -->',

                    '<!-- wp:paragraph -->',
                    '<p>' . wp_kses_post( self::ramadan_format_message( $d[5], $fields ) ) . '</p>',
                    '<!-- /wp:paragraph -->',

                    '<!-- wp:heading {"level":3} -->',
                    '<h3><strong>' . __( 'Related scriptures to pray for this people', 'ramadan-2026' ) . '</strong></h3>',
                    '<!-- /wp:heading -->',

                    '<!-- wp:paragraph -->',
                    '<p>' . wp_kses_post( self::ramadan_format_message( $d[6], $fields ) ) . '</p>',
                    '<!-- /wp:paragraph -->',
                ]
            ];
        }
        return $content;
    }
}
