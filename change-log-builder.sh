#!/usr/bin/env bash
previous_tag=0
clean=''
for current_tag in $(git tag --sort=-creatordate)
do
if [ "$previous_tag" != 0 ];then
    tag_date=$(git log -1 --pretty=format:'%ad' --date=short ${previous_tag})
    printf "## ${previous_tag} (${tag_date})\n\n"
#    git log ${current_tag}...${previous_tag} --pretty=format:'*  %s [View](./commits/%H)' --reverse | grep -v Merge
    gitLogs=$(git log ${current_tag}...${previous_tag} --pretty=format:'*  %s [View](./commits/%H)' --reverse)
    fixes=$(grep -i -E "^\*.\sfix" <<< "${gitLogs}")
    if [[ $fixes == *"fix"* ]];then
        printf "### Fixes:\n\n"
        printf "${fixes}"
    fi
    feats=$(grep -i -E "^\*.\sfeat" <<< "${gitLogs}")
    if [[ $feats == *"feat"* ]];then
        printf "### Features:\n\n"
        printf "${feats}"
    fi
    perfs=$(grep -i -E "^\*.\sperf" <<< "${gitLogs}")
    if [[ $perfs == *"perf"* ]];then
        printf "### Improves performance:\n\n"
        printf "${perfs}"
    fi
    refactors=$(grep -i -E "^\*.\srefactor" <<< "${gitLogs}")
    if [[ $refactors == *"refactor"* ]];then
        printf "### Refactoring:\n\n"
        printf "${refactors}"
    fi
    styles=$(grep -i -E "^\*.\sstyle" <<< "${gitLogs}")
    if [[ $styles == *"style"* ]];then
        printf "### Style:\n\n"
        printf "${styles}"
    fi
    tests=$(grep -i -E "^\*.\stest" <<< "${gitLogs}")
    if [[ $tests == *"test"* ]];then
        printf "### Style:\n\n"
        printf "${tests}"
    fi
    docss=$(grep -i -E "^\*.\sdocs" <<< "${gitLogs}")
    if [[ $docss == *"docs"* ]];then
        printf "### Documentation:\n\n"
        printf "${docss}"
    fi
    cis=$(grep -i -E "^\*.\sci" <<< "${gitLogs}")
    if [[ $cis == *"ci"* ]];then
        printf "### DevOps:\n\n"
        printf "${cis}"
    fi
    builds=$(grep -i -E "^\*.\sbuild" <<< "${gitLogs}")
    if [[ $builds == *"build"* ]];then
        printf "### Build system or external dependencies:\n\n"
        printf "${builds}"
    fi
    reverts=$(grep -i -E "^\*.\srevert" <<< "${gitLogs}")
    if [[ $reverts == *"revert"* ]];then
        printf "### Reverts:\n\n"
        printf "${reverts}"
    fi
    printf "\n\n"
else
    printf "## Unreleased\n\n"
    git log ${current_tag}...HEAD --pretty=format:'*  %s [View](./commits/%H)' --reverse | grep -v Merge
    printf "\n"
fi
previous_tag=${current_tag}
done
