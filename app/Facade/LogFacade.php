<?php declare(strict_types=1);
namespace Frrame\Facade;
use Monolog\Level;
use Frrame\Component\DBstatement;
/**
 * Custom Logger following psr-3 standards.
 */
class LogFacade{
    /**
     * @param array<string,mixed> $context
     * @param array<string,mixed> $extra
     */
    public function __construct(
		public string $channel,
		public Level $level,
		public string $message,
		public array $context = [],
		public array $extra = [])
	{}
    public function logToDB():bool{
        $record = [
            'channel' => $this->channel,
            'level' => $this->level,
            'message' => $this->message,
            'context' => $this->context,
            'extra' => $this->extra
        ];
        $stmt = DBstatement::run(
            "INSERT INTO logs (channel, message, level, level_name, context, extra) VALUES (:channel, :message, :level, :level_name, :context, :extra)",
            [
                ':channel' => $this->channel,
                ':message' => $this->message,
                ':level' => $this->level->value,
                ':level_name' => $this->level->getName(),
                ':context' => !empty($this->context)?json_encode($this->context):null,
                ':extra' => !empty($this->extra)?json_encode($this->extra):null
            ]
        );
		return $stmt->rowCount() === 1;
    }
	public function logToFile():bool{
		// file_put_contents()
		return true;
	}
	/** @return array<string,mixed> */
	public function toArray():array{
		return get_object_vars($this);
	}
}
