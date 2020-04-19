# Modification of Dean Clatworthy's deploy script: https://github.com/deanc/wordpress-plugin-git-svn
 
# main config
CURRENTDIR=`pwd`
PLUGINSLUG="internet-connection-status"
MAINFILE="internet-connection-status.php" # this should be the name of your main php file in the wordpress plugin

# git config
GITPATH="$CURRENTDIR" # this should be the path to your git repository (trailing slash required)

# svn config
SVNURL="http://plugins.svn.wordpress.org/internet-connection-status" # Remote SVN repo on wordpress.org, with no trailing slash
SVNPATH="tmp/$PLUGINSLUG" # path to your checked out SVN repo. Trailing slash is required and don't add trunk.
SVNUSER="sanzeeb3" # your svn username

# Let's begin...
echo ".........................................."
echo 
echo "Preparing to deploy wordpress plugin"
echo 
echo ".........................................."
echo 

# check version in readme.txt is the same as plugin file
NEWVERSION1=`grep "^Stable tag" $GITPATH/readme.txt | awk '{ print $NF}'`
echo $NEWVERSION1;
NEWVERSION2=`grep "^Version" $GITPATH/$MAINFILE | awk '{ print $NF}'`
echo $NEWVERSION2;

if [ "$NEWVERSION1" != "$NEWVERSION2" ]; then echo "Versions don't match. Exiting...."; exit 1; fi

echo "Versions match in readme.txt and PHP file. Let's proceed..."

if git show-ref --tags --quiet --verify -- "refs/tags/$NEWVERSION1"
	then 
		echo "Version $NEWVERSION1 already exists as git tag. Exiting...."; 
		COMMITMSG="Committing $NEWVERSION1"
	else
		echo "Git version does not exist. Let's proceed..."
		cd $GITPATH
    echo -e "Enter a commit message for this new version: \c"
    read COMMITMSG
    git commit -am "$COMMITMSG"

    echo "Tagging new version in git"
    git tag -a "$NEWVERSION1" -m "Tagging version $NEWVERSION1"
fi

echo "Pushing latest commit to origin, with tags"
git push origin master
git push origin master --tags

echo 
echo "Creating local copy of SVN repo ..."
svn co $SVNURL $SVNPATH

echo "Exporting the HEAD of master from git to the trunk of SVN"
git checkout-index -a -f --prefix=$SVNPATH/trunk/

echo "Ignoring github specific files and deployment script"
svn propset svn:ignore "deploy.sh
README.md
.git
.gitignore" "$SVNPATH/trunk/"

echo "Changing directory to SVN and committing to trunk"
cd $SVNPATH/trunk/
# Add all new files that are not set to be ignored
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs svn add
svn commit --username=$SVNUSER -m "$COMMITMSG"

echo "Creating new SVN tag & committing it"
cd $SVNPATH
svn copy trunk/ tags/$NEWVERSION1/
cd $SVNPATH/tags/$NEWVERSION1
svn commit --username=$SVNUSER -m "Tagging version $NEWVERSION1"

echo "Removing temporary directory $SVNPATH"
rm -fr $SVNPATH/

echo "*** FIN ***"