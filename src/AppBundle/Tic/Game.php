<?php
/**
 * Created by PhpStorm.
 * User: ami
 * Date: 10/29/15
 * Time: 10:01 AM
 */

namespace AppBundle\Tic;


class Game
{
    /** @var  Board */
    private $board;

    private $currentPlayer;

    const STATE_NEW = 0;
    const STATE_IN_PLAY = 1;
    const STATE_TIE = 2;
    const STATE_WON = 3;

    public function start()
    {
        $this->board = new Board();
        $this->currentPlayer = Board::X;
        $this->currentGameState = Board::START_GAME;
    }

    public function isMoveLegal($row, $col)
    {

        return Board::NOTHING == $this->board->getSquare($row, $col);
    }

    public function makeMove($row, $col)
    {
        $this->board->setSquare($row, $col, $this->currentPlayer);
        $this->switchPlayer();
    }

    public function getState()
    {
        if($this->board->isEmpty()) {
            return self::STATE_NEW;
        }

        if($this->isGameWon()) {
             $this->currentGameState = Board::GAME_IS_PLAY;
            $this->drawWonBoxes();

             return self::STATE_WON;
        }
        if($this->isGameTie()) {
            return self::STATE_TIE;
        }
        return self::STATE_IN_PLAY;
    }
    private function drawWonBoxes(){

        $arr = $this->board->getGrid();
        for($i=2;$i>0;$i--){
            $test =0;

            if( $arr[$i][0]===$arr[$i-1][0] && $arr[$i][0] !=='' && $arr[$i-1][0] !==''){
                $test =3;
            }
            elseif($arr[$i][1]===$arr[$i-1][1] && $arr[$i][1] !=='' && $arr[$i-1][1] !=='' ){
                $test =2;
            }
            elseif($arr[$i][2]===$arr[$i-1][2] && $arr[$i][2] !=='' && $arr[$i-1][2] !==''){
                $test =1;
            }
        }
        for($j=count($arr)-1;$j>=0;$j--){
            $results = array_unique($arr[$j]);
            if(count($results) == 1 && $arr[$j][$j]!=''){
                if($j==0){
                    echo "<style> tr:first-child{background-color: yellow;}</style>";
                    return self::STATE_WON;
                }
                if($j==1){
                    echo "<style> tr:nth-child(2){background-color: red;}</style>";
                    return self::STATE_WON;
                }
                if($j==2){
                    echo "<style> tr:nth-child(3){background-color: blue;}</style>";
                    return self::STATE_WON;
                }

            }else{
                if( ($arr[0][0] == $arr[1][1]) && ($arr[1][1] ==$arr[2][2])){
                    echo "<style> tr:first-child>td:first-child{background-color: green;}</style>";
                    echo "<style> tr:nth-child(2)>td:nth-child(2){background-color: green;}</style>";
                    echo "<style> tr:nth-child(3)>td:nth-child(3){background-color: green;}</style>";
                    return self::STATE_WON;
                }
                if( ($arr[0][2] == $arr[1][1]) && ($arr[1][1] ==$arr[2][0])){
                    echo "<style> tr:nth-child(3)>td:first-child{background-color: orange;}</style>";
                    echo "<style> tr:nth-child(2)>td:nth-child(2){background-color: orange;}</style>";
                    echo "<style> tr:first-child>td:nth-child(3){background-color: orange;}</style>";

                    return self::STATE_WON;
                }
            }
        }

        if ($test === 3) {
            echo "<style> td:first-child{background-color: yellow;}</style>";

        }
        elseif ($test=== 2) {
            echo "<style> td:nth-child(2){background-color: red;}</style>";

        }
        elseif ($test === 1) {
            echo "<style> td:nth-child(3){background-color: blue;}</style>";
        }
    }
    private function isGameWon()
    {

        return $this->board->isBoardWon();
    }

    private function isGameTie()
    {
        return !$this->board->isBoardWon() && $this->board->isFull();
    }

    public function getWinner()
    {

        if(self::STATE_WON == $this->getState()) {
            $this->switchPlayer();
            $res = $this->currentPlayer;
            $this->switchPlayer();
            return $res;
        }
        return Board::NOTHING;
    }

    private function switchPlayer()
    {
        if($this->currentPlayer=="x" && $this->currentGameState =="START_GAME"){
                $this->currentGameState = Board::GAME_IS_PLAY;
               header("Refresh:0");
        }
        if(Board::X == $this->currentPlayer) {
            $this->currentPlayer = Board::O;
        } else {
            $this->currentPlayer = Board::X;
        }
    }

    /**
     * @param Board $board
     */
    public function setBoard($board)
    {
        $this->board = $board;
    }

    /**
     * @return Board
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @return mixed
     */
    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }

    /**
     * @param mixed $currentPlayer
     */
    public function setCurrentPlayer($currentPlayer)
    {
        $this->currentPlayer = $currentPlayer;
    }


    public function serialize()
    {
        $res = array(
            'grid' => $this->board->getGrid(),
            'currentPlayer' => $this->currentPlayer
        );

        return json_encode($res);
    }

    public function unserialize($json)
    {
        $this->start();
        $data = json_decode($json, true);
        $this->board->loadBoard($data['grid']);
        $this->currentPlayer = $data['currentPlayer'];
    }

}