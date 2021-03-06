#!/usr/bin/env bash

SCOPE="$1"

if [ -z "$SCOPE" ]; then
  SCOPE="auto"
fi

echo "Using scope $SCOPE"

# We get the next version, without tagging
echo "Getting next version"
nextversion="$(source semtag final -fos $SCOPE)"
echo "Publishing with version: $nextversion"

# Build changelog
printf "# CHANGELOG\n\n" > CHANGELOG.md
printf "[Version: v0.0.1]\n\n" >> CHANGELOG.md
./change-log-builder.sh >> CHANGELOG.md

# Build CONTRIBUTING
printf "# CONTRIBUTORS\n\n" > CONTRIBUTORS.md
printf "[Version: v0.0.1]\n\n" >> CONTRIBUTORS.md
git log --all --format="- %aN <%aE>" | sort -u >> CONTRIBUTORS.md

# We replace the placeholder in the source with the new version
replace="s/^PROG_VERSION=\"[^\"]*\"/PROG_VERSION=\"$nextversion\"/g"
sed -i.bak $replace semtag
# We replace the version in the README file with the new version
replace="s/version-[^-]*-/version-$nextversion-/g"
sed -i.bak "$replace" README.md
replace="s/^\[Version: [^[]*]/[Version: $nextversion]/g"
sed -i.bak "$replace" CHANGELOG.md
sed -i.bak "$replace" CONTRIBUTORS.md
# We remove the backup README.md generated by the sed command
rm semtag.bak
rm README.md.bak
rm CHANGELOG.md.bak
rm CONTRIBUTORS.md.bak

# We add both changed files
if ! git add semtag README.md CHANGELOG.md CONTRIBUTORS.md ; then
  echo "Error adding modified files with new version"
  exit 1
fi

if ! git commit -m "Update readme and info to $nextversion" ; then
  echo "Error committing modified files with new version"
  exit 1
fi

if ! git push ; then
  echo "Error pushing modified files with new version"
  exit 1
fi

# We update the tag with the new version
source semtag final -f -v $nextversion
