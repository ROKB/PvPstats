<?php

function getPlayerGender($guid)
{
  global $db;

  $query = sprintf("SELECT gender FROM characters WHERE guid = %d;", $guid);
  $row = $db->query($query)->fetch_array();

  return $row['gender'];
}

function getPlayerRace($guid)
{
  global $db;

  $query = sprintf("SELECT race FROM characters WHERE guid = %d;", $guid);
  $row = $db->query($query)->fetch_array();

  return $row['race'];
}

function getPlayerClass($guid)
{
  global $db;

  $query = sprintf("SELECT class FROM characters WHERE guid = %d;", $guid);
  $row = $db->query($query)->fetch_array();

  return $row['class'];
}

function getPlayerName($guid)
{
  global $db;

  $query = sprintf("SELECT name FROM characters WHERE guid = %d;", $guid);
  $row = $db->query($query)->fetch_array();

  return $row['name'];
}

function getPlayerColor($guid)
{
  global $db, $alliance_color, $horde_color;

  $query = sprintf("SELECT race FROM characters WHERE guid = %d", $guid);
  $row = $db->query($query)->fetch_row();

  switch ($row[0])
  {
    case 1:
    case 3:
    case 4:
    case 7:
    case 11:
      $color = $alliance_color;
      break;
    case 2:
    case 5:
    case 6:
    case 8:
    case 10:
      $color = $horde_color;
      break;
  }

  return $color;
}

function getGuildColor($guildid)
{
  global $db;

  $query = sprintf("SELECT leaderguid FROM guild WHERE guildid = %d", $guildid);
  $row = $db->query($query)->fetch_row();//*/

  return getPlayerColor($row[0]);
}

function getFactionScores($time_cond, $level_cond)
{
  global $db, $ALLIANCE, $HORDE;

  $score[2];

  if ($time_cond != "")
    $time_cond = "AND " . $time_cond;

  if ($level_cond != "")
    $level_cond = "AND " . $level_cond;

  $query = sprintf("SELECT COUNT(*) FROM pvpstats_faction WHERE faction = %d %s %s UNION SELECT COUNT(*) FROM pvpstats_faction WHERE faction = %d %s %s;",
                   $ALLIANCE, $time_cond, $level_cond, $HORDE, $time_cond, $level_cond);

  $result = $db->query($query);

  $row = $result->fetch_row();
  $score[0] = $row[0];

  $row = $result->fetch_row();

  if ($row != null)
    $score[1] = $row[0];
  else
    $score[1] = $score[0];

  return $score;
}

function getPlayersScores($time_cond, $level_cond)
{
  global $db, $limit, $players_group_and_order, $amory_url;


  if ($time_cond == "" && $level_cond == "")
    $where = "";
  else
    $where = "WHERE";

  if ($time_cond != "" && $level_cond != "")
    $level_cond = "AND " . $level_cond;

  $query = sprintf("SELECT character_guid, count(character_guid) FROM pvpstats_players %s %s %s %s %s",
                   $where,
                   $time_cond,
                   $level_cond,
                   $players_group_and_order,
                   $limit);

  $result = $db->query($query);

  $row = $result->fetch_row();

  if ($row == null)
    return;

  $position = 1;

  printf("<tr><td>%d</td><td><a style=\"color: %s; \" target=\"_blank\" href=\"%s%s\"><strong>%s</strong></a></td><td style=\"min-width: 46px; padding-left: 0; padding-right: 0;\"><img src=\"img/class/%d.gif\"> <img src=\"img/race/%d-%d.gif\"></td><td>%d</td></tr>",
         $position,
         getPlayerColor($row[0]),
         $amory_url,
         getPlayerName($row[0]),
         getPlayerName($row[0]),
         getPlayerClass($row[0]),
         getPlayerRace($row[0]),
         getPlayerGender($row[0]),
         $row[1]);

  $prev_score = $row[1];


  while (($row = $result->fetch_row()) != null)
  {
    if ($prev_score != $row[1])
      $position++;

    printf("<tr><td>%d</td><td><a style=\"color: %s; \" target=\"_blank\" href=\"%s%s\"><strong>%s</strong></a></td><td style=\"min-width: 46px; padding-left: 0; padding-right: 0;\"><img src=\"img/class/%d.gif\"> <img src=\"img/race/%d-%d.gif\"></td><td>%d</td></tr>",
           $position,
           getPlayerColor($row[0]),
           $amory_url,
           getPlayerName($row[0]),
           getPlayerName($row[0]),
           getPlayerClass($row[0]),
           getPlayerRace($row[0]),
           getPlayerGender($row[0]),
           $row[1]);

    $prev_score = $row[1];
  }
}

function getGuildsScores($time_cond, $level_cond)
{
  global $db, $limit, $guilds_group_and_order, $guild_amory_url;


  if ($time_cond == "" && $level_cond == "")
    $where = "";
  else
    $where = "WHERE";

  if ($time_cond != "" && $level_cond != "")
    $level_cond = "AND " . $level_cond;

  $query = sprintf("SELECT guild.name, COUNT(guild.name), guild.guildid FROM pvpstats_players INNER JOIN guild_member ON guild_member.guid = pvpstats_players.character_guid INNER JOIN guild ON guild_member.guildid = guild.guildid %s %s %s %s %s",
                   $where,
                   $time_cond,
                   $level_cond,
                   $guilds_group_and_order,
                   $limit_guilds);

  $result = $db->query($query);

  $row = $result->fetch_row();

  if ($row == null)
    return;

  $position = 1;

  printf("<tr><td>%d</td><td><a style=\"color: %s; \" target=\"_blank\" href=\"%s%s\"><strong>%s</strong></a></td><td>%d</td></tr>",
         $position,
         getGuildColor($row[2]),
         $guild_amory_url,
         $row[0],
         $row[0],
         $row[1]);

  $prev_score = $row[1];


  while (($row = $result->fetch_row()) != null)
  {
    if ($prev_score != $row[1])
      $position++;

    printf("<tr><td>%d</td><td><a style=\"color: %s; \" target=\"_blank\" href=\"%s%s\"><strong>%s</strong></a></td><td>%d</td></tr>",
           $position,
           getGuildColor($row[2]),
           $guild_amory_url,
           $row[0],
           $row[0],
           $row[1]);

    $prev_score = $row[1];
  }
}

?>
