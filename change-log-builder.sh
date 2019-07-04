#!/usr/bin/env bash
previous_tag=0
for current_tag in $(git tag --sort=-creatordate)
do

if [ "$previous_tag" != 0 ];then
    tag_date=$(git log -1 --pretty=format:'%ad' --date=short ${previous_tag})
    printf "## ${previous_tag} (${tag_date})\n\n"
    git log ${current_tag}...${previous_tag} --pretty=format:'*  %s [View](./commits/%H)' --reverse | grep -v Merge
    printf "\n\n"
fi
previous_tag=${current_tag}
done
# git log $(git describe --tags --abbrev=0)..HEAD --pretty=format:"%s" | grep -i -E "^(\[INTERNAL\]|\[FEATURE\]|\[FIX\]|\[DOC\])*\[FEATURE\]"
#git log `git describe --tags --abbrev=0`..HEAD --pretty=format:"  * %s"
#git log --all --format="%aN <%aE>" | sort -u
