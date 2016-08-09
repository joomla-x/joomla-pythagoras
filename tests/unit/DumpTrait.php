<?php
namespace Joomla\Tests\Unit;

trait DumpTrait
{
	/**
	 * @param \Exception $e
	 *
	 * @return string
	 */
	protected function dump($e)
	{
		$msg           = '';
		$fmt           = "%s in %s(%d)\n";
		$traceAsString = '';

		while ($e instanceof \Exception)
		{
			$message       = $e->getMessage();
			$file          = $e->getFile();
			$line          = $e->getLine();
			$traceAsString = $e->getTraceAsString();
			$e             = $e->getPrevious();

			$msg .= sprintf($fmt, $message, $file, $line);
		}

		return $msg . "\n" . $traceAsString;
	}

	protected function dumpVar($obj, $dive = false, $indent = '')
	{
		$res = $indent;

		if (is_object($obj))
		{
			$res .= get_class($obj) . " {\n";
			$values = get_object_vars($obj);
		}
		elseif (is_array($obj))
		{
			$res .= "Array {\n";
			$values = $obj;
		}
		else
		{
			return var_export($obj, true);
		}

		foreach ($values as $key => $value)
		{
			if (is_object($value))
			{
				$res .= $indent . "    [$key] => " . ($dive ? $this->dumpVar($value, false, $indent . '    ') : get_class($value)) . "\n";
			}
			elseif (is_array($value))
			{
				$res .= $indent . "    [$key] => " . ($dive ? $this->dumpVar($value, false, $indent . '    ') : 'Array') . " \n";
			}
			else
			{
				$res .= $indent . "    [$key] => " . $this->dumpVar($value, false, $indent . '    ') . "\n";
			}
		}

		$res .= $indent . "}\n";

		return $res;
	}
}
