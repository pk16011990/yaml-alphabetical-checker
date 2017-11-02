<?php

namespace YamlAlphabeticalChecker;

use Symfony\Component\Yaml\Yaml;

class YamlAlphabeticalChecker
{
    /**
     * @param string $pathToYamlFile
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @return bool
     */
    public function parseData($pathToYamlFile)
    {
//        return \Spyc::YAMLLoad(file_get_contents($pathToYamlFile));
        return Yaml::parse(file_get_contents($pathToYamlFile));
    }

    /**
     * @param array $yamlArrayData
     * @return bool
     */
    public function isDataSorted(array $yamlArrayData)
    {
        $yamlArrayDataSorted = $yamlArrayData;
        $this->recursiveKsort($yamlArrayDataSorted);

        return $yamlArrayData === $yamlArrayDataSorted;
    }

    /**
     * @param array $yamlArrayData
     * @param string $pathToYamlFile
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @return string[]
     */
    public function sortData(array $yamlArrayData, $pathToYamlFile)
    {
        $this->recursive_ksort($yamlArrayData);
        $yamlData = \Spyc::YAMLDump($yamlArrayData, 4);
//        $yamlData = Yaml::dump($yamlArrayData, 4, 2);
        $pathToYamlFile2 = substr_replace($pathToYamlFile, '2', -4, 0);
        file_put_contents($pathToYamlFile2, $yamlData);
    }

    /**
     * @param array $array
     */
    private function recursiveKsort(array &$array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $this->recursiveKsort($value);
            }
        }
        ksort($array);
    }
}
