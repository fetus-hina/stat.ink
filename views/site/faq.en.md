## What is stat.ink?

stat.ink is a web application (web service) that stores and aggregates the results of the Splatoon
series.

Users use a separate "data collection app" to send data to stat.ink.

## Is stat.ink related to SplatNet 2/3?

Directly, NO.

stat.ink retrieves data from the "data collection app" and displays it.
stat.ink does not care how the app collects data.

In fact, most of the results of Splatoon 2/3 are recorded by converting the data from SplatNet 2/3,
but that's because users are using such data collection apps.

(Originally, stat.ink was a "data recording and visualization tool for IkaLog." We still don't
assume the existence of SplatNet.)

## Does stat.ink violate Nintendo's terms of service?

Directly, NO. As noted above, stat.ink is not an app that "illegally" uses SplatNet 2/3.

Users can use the "data collection app" to send data from SplatNet 2/3 to stat.ink, but whether the
app is affected by the statement depends on the implementation of the app.

## What represents stat.ink's statistics?

We don't know.

In the relationship between kills/deaths, and win rate, for example, you can see things like "if you
die 4 times or more, your win rate is low" or "if your kill ratio is much less than 1, your win rate
is low" as data.

However, we do not know and have not analyzed whether it is "if you don't die, you will win" or "if
you can create a situation where you don't die, you will win" or "if you can find teammates who can
create a situation where you don't die, you will win."

Needless to say, "100%" as data does not mean that you will win if you reach that K/D.

## Is stat.ink's statistics biased?

Yes. Of course. Absolutely.

stat.ink only creates aggregate information based on the data sent to us.
The vast majority of Splatoon series players are not included in this data, so it is naturally.

Specifically, there are almost no "light users" among stat.ink users, and there are almost no "super
hardcore users."

Therefore, the Splatter Shot Jr., which is likely to be used a lot in the ultra-light segment, will
probably come out lower than the actual number, and the win rate for weapons that require
considerable skill, such as the Splatana Stamper and Squeezer, will be lower.

Note that in order to prevent sender bias, most of the overall statistics exclude data from stat.ink
user and aggregate data from the remaining 7 players each battle.
The community outside us often says "stat.ink statistics are stat.ink user statistics" and are
therefore biased, which is incorrect.

However, there is a slight bias, especially in Splatoon 3, due to the specifications of the
matchmaking system, which makes stat.ink users "call out" certain weapon to their opponents.

## Is stat.ink's statistics reliable?

More reliable than your senses.

Even if you play the game as hard as you can, your opponents are a very small group, determined by
your ability and weaponry.

Unless you are only interested in who you are playing against, if you are looking at the data a
little more broadly, the stats on stat.ink are more reliable than you feel.

## What kind of "data collection apps" are available?

  - {icon:splatoon3} Splatoon 3
    - [s3s](https://github.com/frozenpandaman/s3s) - Windows, macOS, Linux
    - [s3si.ts](https://github.com/spacemeowx2/s3si.ts) - Windows, macOS, Linux

  - {icon:splatoon2} Splatoon 2
    - [splatnet2statink](https://github.com/frozenpandaman/splatnet2statink#splatnet2statink) - Windows, macOS, Linux
    - SquidTracks - Windows, macOS, Linux
    - IkaRec 2 - Android

  - {icon:splatoon1} Splatoon
    - [IkaLog](https://github.com/hasegaw/IkaLog/wiki/en_WinIkaLog) - Windows, macOS, Linux
    - IkaRec - Android

## I want to do my own statistics based on stat.ink data

You can download the data from [Download page](https://stat.ink/downloads).

Your own data can be downloaded from "Profile and Settings" page after logging in.

## I want to make my app compatible with stat.ink

You can use our API for free.
