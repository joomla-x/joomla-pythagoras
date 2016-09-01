<?php
namespace Joomla\Tests\Unit;

use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Repository\RepositoryInterface;

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

	/**
	 * @param object             $object
	 * @param IdAccessorRegistry $idAccessorRegistry
	 *
	 * @return string
	 */
	protected function dumpEntity($object, $idAccessorRegistry = null)
	{
		if (is_null($object) || is_scalar($object))
		{
			return var_export($object, true);
		}

		$res = get_class($object) . " Object\n{\n";
		foreach (get_object_vars($object) as $key => $value)
		{
			if (is_object($value))
			{
				if ($value instanceof RepositoryInterface)
				{
					$res .= "    [$key] => Repository for " . $value->getEntityClass() . "\n";
				}
				else
				{
					$res .= "    [$key] => " . get_class($value);
					if (!is_null($idAccessorRegistry))
					{
						$res .= ':' . $idAccessorRegistry->getEntityId($value);
					}
					$res .= "\n";
				}
			}
			elseif (is_array($value))
			{
				$res .= "    [$key] => Array\n    {\n";
				foreach ($value as $k => $v)
				{
					if (is_object($v))
					{
						$res .= "        [$k] => " . get_class($v) . "\n";
					}
					elseif (is_array($v))
					{
						$res .= "        [$k] => Array(" . count($v) . ")\n";
					}
					else
					{
						$res .= "        [$k] => " . var_export($v, true) . "\n";
					}
				}
				$res .= "    }\n";
			}
			else
			{
				$res .= "    [$key] => " . var_export($value, true) . "\n";
			}
		}
		$res .= "}\n";

		return $res;
	}
}
