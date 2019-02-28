<?php
require_once(dirname(__FILE__)."/Vote.php");

class User implements JsonSerializable {
    private $name;
    private $pwd;
    private $origins; // array
    private $votes; // array

    public function __construct($json = false, $name = '', $pwd = '', $origins = null, $votes = null) {
        if ($json) {
            $this->set(json_decode($json, true));
        } else {
            $this->name = $name;
            $this->pwd = $pwd;
            $this->origins = $origins;
            $this->votes = $votes;
        }
    }

    private function set($data) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sub = array();
                foreach ($value as $sValue) {
                    if ($key === "votes")
                        array_push($sub, new Vote($json = json_encode($sValue)));
                    else
                        array_push($sub, $sValue);
                }
                $value = $sub;
            }
            $this->{$key} = $value;
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getPwd() {
        return $this->pwd;
    }

    /**
     * @return string[]
     */
    public function getOrigins() {
        return $this->origins;
    }

    /**
     * @return Vote[]
     */
    public function getVotes() {
        return $this->votes;
    }

    /**
     * If the user already voted changes the vote, if not adds it
     * @param Vote $vote : The new Vote
     * @return int: The difference between old and new vote
     */
    public function vote(Vote $vote) {
        if ($this->votes === null) {
            $this->votes = array();
        }

        $found = false;
        $diff = 0;
        foreach ($this->votes as $i => $v) {
            if ($v->getId() == $vote->getId()) {
                $diff = $vote->getVote() - $v->getVote();
                $this->votes[$i] = $vote;
                $found = true;
            }
        }
        if (!$found) {
            array_push($this->votes, $vote);
            $diff = $vote->getVote();
        }

        return $diff;
    }

    public function jsonSerialize() {
        return [
            'name' => $this->getName(),
            'pwd' => $this->getPwd(),
            'origins' => $this->getOrigins(),
            'votes' => $this->getVotes()
        ];
    }
}
