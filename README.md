<p align="center">
    <a href="https://github.com/Terpz710/KDR-PE"><img src="https://github.com/Terpz710/KDR-PE/blob/stable/icon.png"></img></a><br>
    <b>KDR system for Pocketmine-MP</b>

# Description

A [Pocketmine-MP](https://pmmp.io) plugin that adds a KDR managment system, See all the features below!

This plugin also creates a floating text leaderboards that shows off the top kills, deaths and killstreaks on the server as well!

**KDR-PE had a huge code rewrite on June 30, 2026**

This rewrite fixes 99.99% of the bugs from v1.0.6 rewrite...

# Features 

* Kill tracker
* Death tracker
* Killstreak system
* KDR system
* Topkill system
* TopDeath system
* TopKillStreak system
* See other players kills, deaths, killstreak and KDRS
* CREATE A TOP KILL/DEATH/KILLSTREAK LEADERBOARD (Floating Text)!

# Permissions/Commands
**/kdr command**
```php
comandLabel: kdr
commandDescription: Checkout your current KDR stats
commandUsage: /kdr
commandAliases: none
commandPermission: kdrpe.stats

// This command can be ran by everyone not just operators
```

**/seekdr command**
```php
commandLabel: seekdr
commandDescription: Checkout someone else's current KDR stats
commandUsage: /seekdr <player: string>
commandAliases: none
commandPermission: kdrpe.otherstats

// This command can be ran by everyone not just operators
```

**/leaderboard command**
```php
commandLabel: leaderboard
commandDescription: Fetches the leaderboard for kill, death and killstreak
commandUsage: /leaderboard <type: string>
commandAliases: none
commandPermission: kdrpe.leaderboard

// This command can ONLY be ran by just operators!
```