<?php
/**
 * User: Lennart
 * Date: 16-5-13
 * Time: 22:03
 */
    require_once(__DIR__ . '/../connect.php');
    require_once(__DIR__ . '/Spelers.php');
    require_once(__DIR__ . '/Wedstrijd.php');
    require_once(__DIR__ . '/Speler.php');
    require_once(__DIR__ . '/Speeldag.php');
    require_once(__DIR__ . '/Seizoen.php');
class Ranking {

    function getRanking($seizoen_id = null, $speeldag_id = null)
    {
        $this->checkSpeeldagenSeizoen($seizoen_id, $speeldag_id);

        $ranking = Array();
        $huidigeRankingstring = '';
        $vorigeRankingString = '';
        if($seizoen_id == null)
        {
            //Geen seizoenen gevonden
            return $ranking;
        }

        //Geen speeldag voor dit seizoen
        //Huidige ranking = basispunten
        if($speeldag_id == null)
        {
            $huidigeRankingstring = "SELECT ISP.id AS speler_id, ISP.naam AS naam, ISP.voornaam as voornaam, ISP.geslacht AS geslacht, ISP.jeugd as jeugd,ISPS.basispunten AS gemiddelde
                                  FROM  intra_spelerperseizoen ISPS
                                  INNER JOIN intra_spelers ISP ON ISP.id = ISPS.speler_id
                                  ORDER BY gemiddelde DESC;";

            //Nu: Vorig seizoen of niet?
            $seizoen = new Seizoen();
            $seizoenen = $seizoen->get_seizoenen();
            if(count($seizoenen) > 1) {
                //We hebben een vorig seizoen
                $seizoen->id = $seizoenen[1]->id;

                $speeldagen = $seizoen->getspeeldagen();
                end($speeldagen);
                $speeldag = prev($speeldagen);
                $vorigeRankingString = sprintf("SELECT @curRank := @curRank +1 AS rank, speler_id
                                                    FROM (
                                                            SELECT ISPS.speler_id AS speler_id, ISPS.gemiddelde AS gemiddelde
                                                            FROM intra_spelerperspeeldag ISPS
                                                            WHERE (ISPS.speeldag_id =  '%s')
                                                    ORDER BY gemiddelde DESC)t,
                                                    (SELECT @curRank :=0)r;",
                    mysql_real_escape_string($speeldag->id));
            }
        }
        else
        {
            $huidigeRankingstring = sprintf("SELECT ISP.id AS speler_id, ISP.naam AS naam, ISP.voornaam as voornaam, ISP.geslacht AS geslacht, ISP.jeugd as jeugd, ISPS.gemiddelde AS gemiddelde
                                  FROM  intra_spelerperspeeldag ISPS
                                  INNER JOIN intra_spelers ISP ON ISP.id = ISPS.speler_id
                                  WHERE (
                                          ISPS.speeldag_id = '%s'
                                        )
                                  ORDER BY gemiddelde DESC;",
                mysql_real_escape_string($speeldag_id));

            $speeldag = new Speeldag();
            $speeldag->get($speeldag_id);
            if($speeldag->speeldagnummer == 1){

                $vorigeRankingString = "SELECT @curRank := @curRank +1 AS rank, speler_id
                                    FROM (
                                            SELECT ISPS.id AS speler_id, ISPS.basispunten AS gemiddelde
                                            FROM intra_spelerperseizoen ISPS
                                            ORDER BY gemiddelde DESC
                                            )t, (SELECT @curRank :=0)r";
            }
            else
            {
                $vorigespeeldagnummer = $speeldag->speeldagnummer -1;
                $vorigeRankingString = sprintf("SELECT @curRank := @curRank +1 AS rank, speler_id
                                                FROM (
                                                        SELECT ISPS.speler_id AS speler_id, ISPS.gemiddelde AS gemiddelde
                                                        FROM intra_spelerperspeeldag ISPS
                                                        INNER JOIN intra_speeldagen ISP ON ISP.id = ISPS.speeldag_id
                                                        WHERE (ISP.seizoen_id =  '%s' AND ISP.speeldagnummer =  '%s')
                                                        ORDER BY gemiddelde DESC)t,
                                                (
                                                  SELECT @curRank :=0
                                                )r",
                    mysql_real_escape_string($seizoen_id),mysql_real_escape_string($vorigespeeldagnummer));

            }
        }

        $huidigeRanking = mysql_query($huidigeRankingstring);
        $ranking = array();

        while ($ranking_array = mysql_fetch_array($huidigeRanking)) {
            $ranking[] = $ranking_array;
        }
        $beide_rankings = Array();
        if($vorigeRankingString != "")
        {
            $vorigeRankingArray = array();
            $vorigeRanking = mysql_query($vorigeRankingString);

            while ($ranking_array = mysql_fetch_array($vorigeRanking)) {
                $vorigeRankingArray[$ranking_array["speler_id"]] = $ranking_array["rank"];
            }
            $beide_rankings["vorigeRanking"] = $vorigeRankingArray;

        }

        $beide_rankings["ranking"] = $ranking;

        return $beide_rankings;


    }
    function getAlgemeneRanking($seizoen_id = null, $speeldag_id = null)
    {
        $this->checkSpeeldagenSeizoen($seizoen_id,$speeldag_id);

        $query = sprintf("SELECT ISP.id AS speler_id, ISP.naam AS naam, ISP.voornaam as voornaam, ISP.geslacht AS geslacht, ISP.jeugd as jeugd ISPS.gemiddelde AS gemiddelde
                                  FROM  intra_spelerperspeeldag ISPS
                                  INNER JOIN intra_spelers ISP ON ISP.id = ISPS.speler_id
                                  WHERE (
                                          ISPS.speeldag_id = '%s'
                                        )
                                  ORDER BY gemiddelde DESC;",
            mysql_real_escape_string($speeldag_id));

        $resultaat = mysql_query($query);

        $ranking = array();

        while ($ranking_array = mysql_fetch_array($resultaat)) {

            $ranking[] = $ranking_array;
        }

        return $ranking;
    }

    private function checkSpeeldagenSeizoen(&$seizoen_id, &$speeldag_id)
    {
        if($seizoen_id == null)
        {
            $seizoen = new Seizoen();
            $seizoen->get_huidig_seizoen();
            $seizoen_id = $seizoen->id;
        }
        if($speeldag_id == null)
        {
            $laatste_speeldag = new Speeldag();
            $laatste_speeldag->get_laatste_speeldag($seizoen_id);
            $speeldag_id = $laatste_speeldag->id;
        }
    }

}