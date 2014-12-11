<?php
class SessionData extends Eloquent
{

    protected $table = 'session_data';

    public function session()
    {
        return $this->belongsTo('CoachingSession', 'session_id', 'id');
    }
}

?>
