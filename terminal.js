/**
 * JavaScript / PHP Web Terminal
 * Adam Shannon
 * 2010-04-19
 */
 
// Declare the global elements
var 
	terminal = document.getElementsByTagName('textarea')[0],
	submit = document.getElementById('submit'),
	xhr = new XMLHttpRequest(),
	base_url = 'commands.php?string=';
	
// Fill out the directory structure
var Filesystem = {

	/**
	 * Some system level variables.
	 */
	path: '',
	user: '',
	host: '',
	
	/**
	 * Binaries
	 */
	bin: {
		cal: function () {
		
		},
		
		/**
		 * Currently unused
		 */
		cd: function (newPath) {alert(newPath);
			
			// Split the path apart
			var path_split = newPath.split('/');alert(path_split);
			var tmp_path = this;
			
				for (var i = 0; i < path_split.length; i++) {
					if (path_split[i] !== '') {
						tmp_path = tmp_path[path_split[i]];
					}
				}
			
			// Verify that the path is correct.
			if (tmp_path !== undefined) {
				this.path = tmp_path;
			} else {
				printCommnand('This path is invalid');
			}
			
		},
		
		copy: function () {
		
		},
		
		/**
		 * Show all objects in a directory
		 */
		dir: function () {
			
			// Use the current working directory
			var current_dir = Filesystem.path;
			
			printNewLine();
			
			for (var i = 0; i < current_dir.length; i += 4) {
				printCommand(
					current_dir[i + 0] + ' ' + 
					current_dir[i + 1] + ' ' +
					current_dir[i + 2] + ' ' +
					current_dir[i + 3]
				);
				
				printNewLine(); 
			}
			
		},
		
		disk: function () {
		
		},
		
		grep: function () {
		
		},
		
		info: function () {
		
		},
		
		ip: function () {
		
		},
		
		logout: function () {
		
		},
		
		login: function () {
		
		},
		
		move: function () {
		
		},
		
		newFile: function () {
		
		},
		
		newFolder: function () {
		
		},
		
		openFile: function () {
		
		},
		
		pwd: function () {
		
		},
		
		saveFile: function () {
		
		},
		
		sudo: function () {
		
		}
		
	},
	
	/**
	 * Devices
	 */
	dev: {
		'null': {},
		
		random: function () {
			return Math.random();
		}
	},
	
	/**
	 * Config / Setting filess
	 */ 
	etc: {
		levels: {
			0: 'Guest',
			2: 'User',
			5: 'Admin' 
		}
	},
	
	/**
	 * User folders and files
	 */
	home: {
	
		/**
		 * Everything for the guest user
		 */
		guest: {
			
			// Applications
			apps: {},
		
			// Documents
			docs: {
				welcome: {
					content: 'Welcome to a sample terminal'
				},
				
				todo: {
					content: 'Pay bills\nMove Trash'
				}
			},
			
			// Mail
			mail: {},
			
			// Music
			music: {},
			
			// Pictures
			pics: {}
			
		},
		
		/**
		 * Everything for the root user
		 */
		root: {
		
			// Applications
			apps: {},
		
			// Documents
			docs: {
				welcome: {
					content: 'Welcome to a sample terminal'
				},
				
				todo: {
					content: 'Pay bills\nMove Trash'
				}
			},
			
			// Mail
			mail: {},
			
			// Music
			music: {},
			
			// Pictures
			pics: {}
			
		}
	},
	
	/**
	 * The kernel
	 */
	kernel: {
		
		/**
		 * Boot the kernel
		 */
		boot: function () {
			
			// Set the user, host, and path.
			Filesystem.host = 'laptop';
			Filesystem.path = '/';
			Filesystem.user = 'guest';
			
			// Calculate how much space is used.
			var spaceUsed = 0;
			
			// Find the space for each of the types of files.
			// We will total the amount for all users and the system.
			var userCount = Filesystem.vars.users.length;
			
				for (var n = 0; n < userCount; n++) {
					spaceUsed += Filesystem.kernel.calcSpaceForUser(Filesystem.vars.users[n]);
				}
				
			Filesystem.kernel.disk.spaceUsed = spaceUsed;
			
		},
		
		/**
		 * Calculate the space for a directory.
		 */
		calcSpaceForDir: function (user, type) {
			
			// Find out how many files are in the directory.
			var numFiles = Filesystem.vars[user][type].length;
			var totalSize = 0;
			
			for (var n = 0; n < numFiles; n++) {
				totalSize += Filesystem.kernel.calcSpaceForFile(user, type, Filesystem.vars[user][type][n]);
			}
			
			return totalSize;
			
		},
		
		/**
		 * Calculate the space used for a file
		 */
		calcSpaceForFile: function (user, type, file) {
			return Filesystem.home[user][type][file].content.length;
		},
		
		/**
		 * Calculate the space used by a user
		 */
		calcSpaceForUser: function (user) {
			
			var spaceUsed = 0;
			
			// Total a user's space usage
			spaceUsed += Filesystem.kernel.calcSpaceForDir(user, 'apps');
			spaceUsed += Filesystem.kernel.calcSpaceForDir(user, 'docs');
			spaceUsed += Filesystem.kernel.calcSpaceForDir(user, 'mail');
			spaceUsed += Filesystem.kernel.calcSpaceForDir(user, 'music');
			spaceUsed += Filesystem.kernel.calcSpaceForDir(user, 'pics');
			
			return spaceUsed;
		},
		
		/**
		 * Disk properties
		 */
		disk: {
			// 1mb
			totalSpace: 1000000,
			spaceUsed: 0
			
		},
		
		/**
		 * Create a new user
		 */
		newUser: function (name) {
			
		},
		
		/**
		 * Delete a user
		 */
		deleteUser: function (name) {
		
		},
		
		/**
		 * newFolder(String path, String folder)
		 * Create a new folder at FileSystem::"path/folder".
		 *   - Given that the path is correct
		 *   - Given that one like that doesn't exist
		 *
		 * @parm: String path
		 * @parm: String folder
		 * @return: bool
		 */
		newFolder: function (path, folder) {
		
		},
		
		/**
		 * deleteFolder(String path, String folder)
		 * Delete a folder at FileSystem::"path/folder"
		 *   - Given that the path is correct
		 *
		 * @parm: String path
		 * @parm: String folder
		 * @return: bool
		 */
		deleteFolder: function (path, folder) {
		
		},
		
		/**
		 * isDir(String path, String folder)
		 * Return a bool to represent if FileSystem::"path/folder" is a valid folder
		 *
		 * @parm: String path
		 * @parm: String folder
		 * @return: bool
		 */
		isDir: function (path, folder) {
		
		},
		
		/**
		 * parsePath(String path)
		 * Parse a path and return the directories in an array.
		 *
		 * @parm: String path
		 * @return: array
		 */
		parsePath: function (path) {
			
		},
		
		/**
		 * parseCommand(String command)
		 * Parse the string and return the command and flags, both in strings.
		 *
		 * @parm: String command
		 * @return: array
		 */
		parseCommand: function (command) {
			command = command.split(' ');
			
			// Now rebuild the flags
			var flags = '';
			var flags_len = command.length;
			
				for (var n = 1; n < flags_len; n++) {
					flags += command[n] + ' ';
				}
			
			return [command[0], flags.substr(0, flags.length - 1)];
		},
		
		/**
		 * parseFlag(String flag, String flags)
		 * Parse and return the value for the flag.
		 *
		 * @parm: String flag
		 * @parm: String flags
		 * @return: String
		 */
		parseFlag: function (flag, flags) {
			
			// For now just send the request to the server
			var xhr = new XMLHttpRequest();
				xhr.open('GET', 'commands.php?parseFlag&flag0=' + flag[0] + '&flag1=' + flag[1] + '&flags=' + flags, false);
				xhr.send(null);
				
			return xhr.responseText;
		}
	},
	
	/**
	 * Libraries
	 */
	libs: {
		
		/**
		 * This is a library for logs.
		 */
		logs: {
			
			/**
			 * getLogs(String user)
			 * Get all of the logs for a user
			 *
			 * @parm: String user
			 * @return: array
			 */
			getLogs: function (user) {
				return Filesystem.logs.users[user].commands;
			},
			
			/**
			 * getLog(Int id)
			 * Get the log by an ID
			 *
			 * @parm: Int id
			 * @return: String
			 */
			getLog: function (id) {
				return Filesystem.logs.users[user].commands[id];
			},
			
			/**
			 * newEntry(String line)
			 * Push a new log (line) to the correct array of logs.
			 *
			 * @parm: String line
			 * @return: undefined
			 */
			newEntry: function (line, user) {
			
				user = user || Filesystem.user;
				
				// Store the line in the array for the current user.
				Filesystem.logs.users[user].commands.push(line);
				
				// Update the last command.
				Filesystem.logs.users[user].last_command = Filesystem.logs.users[Filesystem.user].commands.length;
				
			},
			
			/**
			 * lastCommand(Int action)
			 * Return the last command for the current user.
			 *
			 * @parm: Int action
			 * @return: String
			 */
			lastCommand: function (action) {
				
				// Set a simple variable for the latest command
				var last = Filesystem.logs.users[Filesystem.user].last_command + action;
				
				// Change the command internally
				Filesystem.logs.users[Filesystem.user].last_command = last;
				
				// Send the command to the screen
				return Filesystem.logs.users[Filesystem.user].commands[last];
			}
			
		}
		
	},
	
	/**
	 * Logs
	 */
	logs: {
		users: {
			guest: {
				commands: [],
				errors: [],
				last_command: 0
			},
			
			root: {
				commands: [],
				errors: [],
				lasst_command: 0
			}
		}
	},
	
	/**
	 * Temporary Files
	 */
	tmp: {},
	
	/**
	 * User accounts and settings
	 */
	users: {
		guest: {
			firstName: 'Mr.',
			lastName: 'Guest',
			password: ''
		},
		
		root: {
			firstName: 'John',
			lastName: 'Doe',
			password: 'abc123'
		}
	},
	
	/**
	 * Variable Files
	 */
	'vars': {
		// List of all users
		users: ['guest', 'root'],
	
		// Variable files for "guest"
		guest: {
			apps: [],
			docs: ['welcome', 'todo'],
			mail: [],
			music: [],
			pics: []
		},
		
		// Variable files for "root"
		root: {
			apps: [],
			docs: ['welcome', 'todo'],
			mail: [],
			music: [],
			pics: []
		}
	}
	
};

/**
 * moveCursorToEnd()
 * Move the cursor to the end of the element.
 *
 * @credit: http://is.gd/create.php
 *
 * @parm: HTMLElement
 * @return: undefined
 */
function moveCursorToEnd (el) {

	// Move to the end of the element
	el.setSelectionRange(0, el.value.length);
	el.focus();
	
}

/**
 * scrollToBottom()
 * Scroll to the bottom of a textarea
 *
 * @parm: HTMLElement
 * @return: undefined
 */
function scrollToBottom(el) {

    el.scrollTop = el.scrollHeight; 
	
}

/**
 * printCommand(cmd)
 * Print a command or the submit message.
 *
 * @parm: null
 * @return: undefined
 */
function printCommand(cmd) {

	// Get the command to show
	var command = cmd || submit.value;

	// Pull from the global namespace
	terminal.innerHTML += Filesystem.user + '@' + Filesystem.host + ':' + Filesystem.path + '$ ' + command + '\n';

}

/**
 * printNewLine()
 * Print a new line to the terminal.
 *
 * @parm: null
 * @return: undefined
 */
function printNewLine() {
	terminal.innerHTML += '\n';
}

// Listen for the keys being pressed
document.onkeydown = function (event) {
	
	if (event.which === 13) {
		
		// If the command is to clear then do it and don't send a request.
		if (!(/^(clear|cls)/).test(submit.value)) {
		
			// Check for commands that require a call to the backend.
			if ((/^(date|diff|echo|get|json|math|mail|md5|pass|random|sha1|sms|time|parseurl|whois)/).test(submit.value)) {
			
				// Load the xhr
				xhr.open('GET', base_url + encodeURIComponent(submit.value), false);
				xhr.send(null);
				
				// alert(xhr.responseText);
				
				// Append the terminal
				printCommand();
				terminal.innerHTML += xhr.responseText + '\n\n';
				
				// Scroll to the bottom of the textarea
				scrollToBottom(terminal);
				
			// Otherwise, check that it's a local command.
			} else if ((/^(cal|cd|clear|copy|dir|disk|grep|info|ip|login|logout|move|new|open|pwd|save|sudo)/).test(submit.value)) {
			
				// Print the command for anything that doesn't match ^cd\s
				if (!(/^cd\s/).test(submit.value)) {
					printCommand();
				}
				
				// Parse the command and flag
				var _cmd_ = Filesystem.kernel.parseCommand(submit.value);
				var _command = _cmd_[0];
				var flags = _cmd_[1];
				
				// Process the local commands.
				switch (true) {
					
					/**
					 * cd
					 */
					case (/^cd/).test(submit.value):
						
						// Find out what the path is
						var path = flags;
						
						// TODO :: Validate that the path is correct.
						if (path.indexOf('/') === 0) {
						
							// Move to the root directory.
							Filesystem.path = path;
							
						} else if (path.indexOf('~') === 0) {
						
							// Move to the current user directory.
							Filesystem.path = '/home/' + Filesystem.user + path.substr(1);
							
						} else if (path.indexOf('./') === 0) {
						
							// Move back one directory.
							var dirs = Filesystem.path.split('/');
							var newPath = '';
							
							// Remove a trailing slash on the new path
							if (flags.lastIndexOf('/') === flags.length - 1) {
								flags = flags.substr(0, flags.length - 1);
							}
							
							// Only go up to the directory before the last.
							var len = dirs.length - 1;
							
								for (var n = 0; n < len; n++) {
									newPath += dirs[n] + '/';
								}
								
							// Move the user
							newPath += flags.substr(2, flags.length - 1);
							
								if (newPath.lastIndexOf('/') == newPath.length - 1) {
									newPath = newPath.substr(0, newPath.length - 1);
								}
								
							Filesystem.path = newPath;
							
						} else {
						
							// Append a slash if needed.
							if (Filesystem.path.substr(Filesystem.path.length - 1, 1).indexOf('/') !== 0) {
								Filesystem.path += '/' + path;
							} else {
								Filesystem.path += path;
							}
							
						}
						
						// Print the command to the screen
						printCommand();
						printNewLine();
						
					break;
					
					/**
					 * login
					 */
					case (/^login/).test(submit.value):
						
						// Parse the required flags.
						var user = Filesystem.kernel.parseFlag(['-u', '-user'], flags);
						var pass = Filesystem.kernel.parseFlag(['-p', '-pass'], flags);
						
						// Log the user in, or send a fail message.
						if (Filesystem.users[user].password === pass) {
							
							// Set some system variables to the new user.
							Filesystem.user = user;
							printCommand('User logged in');
							printNewLine();
							
						} else {
						
							// Log the failed attempt.
							Filesystem.libs.logs.newEntry('Failed login', user);
							
						}
					
					break;
					
					/**
					 * logout
					 */
					case (/^logout/).test(submit.value):
					
						// Change the user to guest
						Filesystem.user = 'guest';
						printCommand('User logged out');
						printNewLine();
					
					break;
					
					/**
					 * disk space/usage
					 */
					case (/^disk/).test(submit.value):
						
						// Show the disk information
						terminal.innerHTML += '> ' + Filesystem.kernel.disk.spaceUsed + 'b of ' + Filesystem.kernel.disk.totalSpace + 'b\n';
						terminal.innerHTML += '> Space used for user ' + Filesystem.kernel.calcSpaceForUser(Filesystem.user) + 'b\n';
						printNewLine();
						
					break;
					
				}
				
			} else {
				
				printCommand();
				terminal.innerHTML += '> Error :: Command is invalid.\n\n';
				
			}
			
			// Store the command
			Filesystem.libs.logs.newEntry(submit.value);
			
		} else {
			
			// Clear the terminal
			terminal.innerHTML = '';
			
		}
	
		// Clear out the submit div
		submit.value = '';
		
	}
	
	if (event.which === 38) {
	
		// Fill the submit value with the last submitted - 1 command.
		// submit.value = commands[last_command -= 1] || '';
		submit.value = Filesystem.libs.logs.lastCommand(-1) || '';
		moveCursorToEnd(submit);
		
	}
	
	if (event.which === 40) {
	
		// Fill the submit value with the last submitted - 1 command.
		// submit.value = commands[last_command += 1] || '';
		submit.value = Filesystem.libs.logs.lastCommand(1) || '';
		moveCursorToEnd(submit);
		
	}
	
};
