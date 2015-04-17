<?php
	// quote is key, value is author
	$wisdom = array(
		"Cherish each hour of this day for it can never return." => 'Og Mandino',
		"Be careful about reading health books. You may die of a misprint." => 'Mark Twain',
		"Courage is resistance to fear, mastery of fear - not absence of fear." => 'Mark Twain',
		"I have never let my schooling interfere with my education." => 'Mark Twain',
		"It is better to keep your mouth closed and let people think you are a fool than to open it and remove all doubt." => 'Mark Twain',
		"United we stand, divided we fall." => 'Aesop',
		"No act of kindness, no matter how small, is ever wasted." => 'Aesop',
		"A crust eaten in peace is better than a banquet partaken in anxiety." => 'Aesop',
		"A fanatic is one who can't change his mind and won't change the subject." => 'Winston Churchill',
		"From now on, ending a sentence with a preposition is something up with which I will not put." => 'Winston Churchill',
		"History will be kind to me for I intend to write it." => 'Winston Churchill',
		"Success is the ability to go from one failure to another with no loss of enthusiasm." => 'Winston Churchill',
		"We shall not fail or falter; we shall not weaken or tire. Neither the sudden shock of battle nor the long-drawn trials of vigilance and exertion will wear us down. Give us the tools and we will finish the job." => 'Winston Churchill',
		"Put your hand on a stove for a minute and it seems like an hour. Sit with that special girl for an hour and it seems like a minute. That's relativity." => 'Albert Einstein',
		"To punish me for my contempt for authority, fate made me an authority myself." => 'Albert Einstein',
		"Try not to become a man of success but rather to become a man of value." => 'Albert Einstein',
		"Do not go where the path may lead, go instead where there is no path and leave a trail." => 'Ralph Waldo Emerson',
		"Don't be too timid and squeamish about your actions. All life is an experiment. The more experiments you make the better." => 'Ralph Waldo Emerson',
		"Shallow men believe in luck. Strong men believe in cause and effect." => 'Ralph Waldo Emerson',
		"Tis the good reader that makes the good book." => 'Ralph Waldo Emerson',
		"Drive thy business or it will drive thee." => 'Benjamin Franklin',
		"Early to bed and early to rise makes a man healthy, wealthy, and wise." => 'Benjamin Franklin',
		"He that blows the coals in quarrels that he has nothing to do with, has no right to complain if the sparks fly in his face." => 'Benjamin Franklin'
	);
	
	$quote = array_rand($wisdom);
	echo '<cite>' . $quote . '</cite> <cite class="author">&mdash; ' . $wisdom[$quote] . '</cite>';
?>