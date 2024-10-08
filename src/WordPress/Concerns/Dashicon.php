<?php

namespace Navigator\WordPress\Concerns;

enum Dashicon: string
{
    case ADMIN_APPEARANCE = 'admin-appearance';
    case ADMIN_COLLAPSE = 'admin-collapse';
    case ADMIN_COMMENTS = 'admin-comments';
    case ADMIN_CUSTOMIZER = 'admin-customizer';
    case ADMIN_GENERIC = 'admin-generic';
    case ADMIN_HOME = 'admin-home';
    case ADMIN_LINKS = 'admin-links';
    case ADMIN_MEDIA = 'admin-media';
    case ADMIN_MULTISITE = 'admin-multisite';
    case ADMIN_NETWORK = 'admin-network';
    case ADMIN_PAGE = 'admin-page';
    case ADMIN_PLUGINS = 'admin-plugins';
    case ADMIN_POST = 'admin-post';
    case ADMIN_SETTINGS = 'admin-settings';
    case ADMIN_SITE_ALT = 'admin-site-alt';
    case ADMIN_SITE_ALT_2 = 'admin-site-alt2';
    case ADMIN_SITE_ALT_3 = 'admin-site-alt3';
    case ADMIN_SITE = 'admin-site';
    case ADMIN_TOOLS = 'admin-tools';
    case ADMIN_USERS = 'admin-users';
    case AIRPLANE = 'airplane';
    case ALBUM = 'album';
    case ALIGN_CENTER = 'align-center';
    case ALIGN_FULL_WIDTH = 'align-full-width';
    case ALIGN_LEFT = 'align-left';
    case ALIGN_NONE = 'align-none';
    case ALIGN_PULL_LEFT = 'align-pull-left';
    case ALIGN_PULL_RIGHT = 'align-pull-right';
    case ALIGN_RIGHT = 'align-right';
    case ALIGN_WIDE = 'align-wide';
    case AMAZON = 'amazon';
    case ANALYTICS = 'analytics';
    case ARCHIVE = 'archive';
    case ARROW_DOWN_ALT = 'arrow-down-alt';
    case ARROW_DOWN_ALT_2 = 'arrow-down-alt2';
    case ARROW_DOWN = 'arrow-down';
    case ARROW_LEFT_ALT = 'arrow-left-alt';
    case ARROW_LEFT_ALT_2 = 'arrow-left-alt2';
    case ARROW_LEFT = 'arrow-left';
    case ARROW_RIGHT_ALT = 'arrow-right-alt';
    case ARROW_RIGHT_ALT_2 = 'arrow-right-alt2';
    case ARROW_RIGHT = 'arrow-right';
    case ARROW_UP_ALT = 'arrow-up-alt';
    case ARROW_UP_ALT_2 = 'arrow-up-alt2';
    case ARROW_UP = 'arrow-up';
    case ARROW_UP_DUPLICATE = 'arrow-up-duplicate';
    case ART = 'art';
    case AWARDS = 'awards';
    case BACKUP = 'backup';
    case BANK = 'bank';
    case BEER = 'beer';
    case BELL = 'bell';
    case BLOCK_DEFAULT = 'block-default';
    case BOOK_ALT = 'book-alt';
    case BOOK = 'book';
    case BUDDICONS_ACTIVITY = 'buddicons-activity';
    case BUDDICONS_BBPRESS_LOGO = 'buddicons-bbpress-logo';
    case BUDDICONS_BUDDYPRESS_LOGO = 'buddicons-buddypress-logo';
    case BUDDICONS_COMMUNITY = 'buddicons-community';
    case BUDDICONS_FORUMS = 'buddicons-forums';
    case BUDDICONS_FRIENDS = 'buddicons-friends';
    case BUDDICONS_GROUPS = 'buddicons-groups';
    case BUDDICONS_PM = 'buddicons-pm';
    case BUDDICONS_REPLIES = 'buddicons-replies';
    case BUDDICONS_TOPICS = 'buddicons-topics';
    case BUDDICONS_TRACKING = 'buddicons-tracking';
    case BUILDING = 'building';
    case BUSINESSMAN = 'businessman';
    case BUSINESSPERSON = 'businessperson';
    case BUSINESSWOMAN = 'businesswoman';
    case BUTTON = 'button';
    case CALCULATOR = 'calculator';
    case CAMERA_ALT = 'camera-alt';
    case CAR = 'car';
    case CALENDAR_ALT = 'calendar-alt';
    case CALENDAR = 'calendar';
    case CAMERA = 'camera';
    case CARROT = 'carrot';
    case CART = 'cart';
    case CATEGORY = 'category';
    case CHART_AREA = 'chart-area';
    case CHART_BAR = 'chart-bar';
    case CHART_LINE = 'chart-line';
    case CHART_PIE = 'chart-pie';
    case CLIPBOARD = 'clipboard';
    case CLOCK = 'clock';
    case CLOUD_SAVED = 'cloud-saved';
    case CLOUD_UPLOAD = 'cloud-upload';
    case CLOUD = 'cloud';
    case COLUMNS = 'columns';
    case CODE_STANDARDS = 'code-standards';
    case COFFEE = 'coffee';
    case COLOR_PICKER = 'color-picker';
    case CONTROLS_BACK = 'controls-back';
    case CONTROLS_FORWARD = 'controls-forward';
    case CONTROLS_PAUSE = 'controls-pause';
    case CONTROLS_PLAY = 'controls-play';
    case CONTROLS_REPEAT = 'controls-repeat';
    case CONTROLS_SKIPBACK = 'controls-skipback';
    case CONTROLS_SKIPFORWARD = 'controls-skipforward';
    case CONTROLS_VOLUMEOFF = 'controls-volumeoff';
    case CONTROLS_VOLUMEON = 'controls-volumeon';
    case COVER_IMAGE = 'cover-image';
    case DASHBOARD = 'dashboard';
    case DATABASE = 'database';
    case DATABASE_ADD = 'database-add';
    case DATABASE_EXPORT = 'database-export';
    case DATABASE_IMPORT = 'database-import';
    case DATABASE_REMOVE = 'database-remove';
    case DATABASE_VIEW = 'database-view';
    case DESKTOP = 'desktop';
    case DISMISS = 'dismiss';
    case DOWNLOAD = 'download';
    case DRUMSTICK = 'drumstick';
    case EDIT = 'edit';
    case EDIT_LARGE = 'edit-large';
    case EDIT_PAGE = 'edit-page';
    case EDITOR_ALIGNCENTER = 'editor-aligncenter';
    case EDITOR_ALIGNLEFT = 'editor-alignleft';
    case EDITOR_ALIGNRIGHT = 'editor-alignright';
    case EDITOR_BOLD = 'editor-bold';
    case EDITOR_BREAK = 'editor-break';
    case EDITOR_CODE = 'editor-code';
    case EDITOR_CODE_DUPLICATE = 'editor-code-duplicate';
    case EDITOR_CONTRACT = 'editor-contract';
    case EDITOR_CUSTOMCHAR = 'editor-customchar';
    case EDITOR_EXPAND = 'editor-expand';
    case EDITOR_HELP = 'editor-help';
    case EDITOR_INDENT = 'editor-indent';
    case EDITOR_INSERTMORE = 'editor-insertmore';
    case EDITOR_ITALIC = 'editor-italic';
    case EDITOR_JUSTIFY = 'editor-justify';
    case EDITOR_KITCHENSINK = 'editor-kitchensink';
    case EDITOR_LTR = 'editor-ltr';
    case EDITOR_OL_RTL = 'editor-ol-rtl';
    case EDITOR_OL = 'editor-ol';
    case EDITOR_OUTDENT = 'editor-outdent';
    case EDITOR_PARAGRAPH = 'editor-paragraph';
    case EDITOR_PASTE_TEXT = 'editor-paste-text';
    case EDITOR_PASTE_WORD = 'editor-paste-word';
    case EDITOR_QUOTE = 'editor-quote';
    case EDITOR_REMOVEFORMATTING = 'editor-removeformatting';
    case EDITOR_RTL = 'editor-rtl';
    case EDITOR_SPELLCHECK = 'editor-spellcheck';
    case EDITOR_STRIKETHROUGH = 'editor-strikethrough';
    case EDITOR_TABLE = 'editor-table';
    case EDITOR_TEXTCOLOR = 'editor-textcolor';
    case EDITOR_UL = 'editor-ul';
    case EDITOR_UNDERLINE = 'editor-underline';
    case EDITOR_UNLINK = 'editor-unlink';
    case EDITOR_VIDEO = 'editor-video';
    case ELLIPSIS = 'ellipsis';
    case EMAIL_ALT = 'email-alt';
    case EMAIL_ALT_2 = 'email-alt2';
    case EMAIL = 'email';
    case EMBED_AUDIO = 'embed-audio';
    case EMBED_GENERIC = 'embed-generic';
    case EMBED_PHOTO = 'embed-photo';
    case EMBED_POST = 'embed-post';
    case EMBED_VIDEO = 'embed-video';
    case EXCERPT_VIEW = 'excerpt-view';
    case EXIT = 'exit';
    case EXTERNAL = 'external';
    case FACEBOOK_ALT = 'facebook-alt';
    case FACEBOOK = 'facebook';
    case FEEDBACK = 'feedback';
    case FILTER = 'filter';
    case FLAG = 'flag';
    case FOOD = 'food';
    case FORMAT_ASIDE = 'format-aside';
    case FORMAT_AUDIO = 'format-audio';
    case FORMAT_CHAT = 'format-chat';
    case FORMAT_GALLERY = 'format-gallery';
    case FORMAT_IMAGE = 'format-image';
    case FORMAT_QUOTE = 'format-quote';
    case FORMAT_STATUS = 'format-status';
    case FORMAT_VIDEO = 'format-video';
    case FORMS = 'forms';
    case FULLSCREEN_ALT = 'fullscreen-alt';
    case FULLSCREEN_EXIT_ALT = 'fullscreen-exit-alt';
    case GAMES = 'games';
    case GOOGLE = 'google';
    case GOOGLEPLUS = 'googleplus';
    case GRID_VIEW = 'grid-view';
    case GROUPS = 'groups';
    case HAMMER = 'hammer';
    case HEADING = 'heading';
    case HEART = 'heart';
    case HIDDEN = 'hidden';
    case HOURGLASS = 'hourglass';
    case HTML = 'html';
    case ID_ALT = 'id-alt';
    case ID = 'id';
    case IMAGE_CROP = 'image-crop';
    case IMAGE_FILTER = 'image-filter';
    case IMAGE_FLIP_HORIZONTAL = 'image-flip-horizontal';
    case IMAGE_FLIP_VERTICAL = 'image-flip-vertical';
    case IMAGE_ROTATE_LEFT = 'image-rotate-left';
    case IMAGE_ROTATE_RIGHT = 'image-rotate-right';
    case IMAGE_ROTATE = 'image-rotate';
    case IMAGES_ALT = 'images-alt';
    case IMAGES_ALT_2 = 'images-alt2';
    case INDEX_CARD = 'index-card';
    case INFO_OUTLINE = 'info-outline';
    case INFO = 'info';
    case INSERT_AFTER = 'insert-after';
    case INSERT_BEFORE = 'insert-before';
    case INSERT = 'insert';
    case INSTAGRAM = 'instagram';
    case KEYBOARD_HIDE = 'keyboard-hide';
    case LAPTOP = 'laptop';
    case LAYOUT = 'layout';
    case LEFTRIGHT = 'leftright';
    case LIGHTBULB = 'lightbulb';
    case LIST_VIEW = 'list-view';
    case LINKEDIN = 'linkedin';
    case LOCATION_ALT = 'location-alt';
    case LOCATION = 'location';
    case LOCK_DUPLICATE = 'lock-duplicate';
    case LOCK = 'lock';
    case MARKER = 'marker';
    case MEDIA_ARCHIVE = 'media-archive';
    case MEDIA_AUDIO = 'media-audio';
    case MEDIA_CODE = 'media-code';
    case MEDIA_DEFAULT = 'media-default';
    case MEDIA_DOCUMENT = 'media-document';
    case MEDIA_INTERACTIVE = 'media-interactive';
    case MEDIA_SPREADSHEET = 'media-spreadsheet';
    case MEDIA_TEXT = 'media-text';
    case MEDIA_VIDEO = 'media-video';
    case MEGAPHONE = 'megaphone';
    case MENU_ALT = 'menu-alt';
    case MENU_ALT_2 = 'menu-alt2';
    case MENU_ALT_3 = 'menu-alt3';
    case MENU = 'menu';
    case MONEY_ALT = 'money-alt';
    case MICROPHONE = 'microphone';
    case MIGRATE = 'migrate';
    case MINUS = 'minus';
    case MONEY = 'money';
    case MOVE = 'move';
    case NAMETAG = 'nametag';
    case NETWORKING = 'networking';
    case NO_ALT = 'no-alt';
    case NO = 'no';
    case OPEN_FOLDER = 'open-folder';
    case PALMTREE = 'palmtree';
    case PAPERCLIP = 'paperclip';
    case PERFORMANCE = 'performance';
    case PETS = 'pets';
    case PDF = 'pdf';
    case PHONE = 'phone';
    case PINTEREST = 'pinterest';
    case PLAYLIST_AUDIO = 'playlist-audio';
    case PLAYLIST_VIDEO = 'playlist-video';
    case PLUS_ALT = 'plus-alt';
    case PLUS_LIGHT = 'plus-light';
    case PLUS = 'plus';
    case PORTFOLIO = 'portfolio';
    case POST_STATUS = 'post-status';
    case PRESSTHIS = 'pressthis';
    case PRODUCTS = 'products';
    case PLUGINS_CHECKED = 'plugins-checked';
    case PLUS_ALT_2 = 'plus-alt2';
    case PODIO = 'podio';
    case PRINTER = 'printer';
    case PRIVACY = 'privacy';
    case RANDOMIZE = 'randomize';
    case REDDIT = 'reddit';
    case REDO = 'redo';
    case REMOVE = 'remove';
    case REST_API = 'rest-api';
    case RSS = 'rss';
    case SAVED = 'saved';
    case SCHEDULE = 'schedule';
    case SCREENOPTIONS = 'screenoptions';
    case SEARCH = 'search';
    case SHARE_ALT = 'share-alt';
    case SHARE_ALT_2 = 'share-alt2';
    case SHARE = 'share';
    case SHIELD_ALT = 'shield-alt';
    case SHIELD = 'shield';
    case SHORTCODE = 'shortcode';
    case SLIDES = 'slides';
    case SMARTPHONE = 'smartphone';
    case SMILEY = 'smiley';
    case SORT = 'sort';
    case SOS = 'sos';
    case STAR_EMPTY = 'star-empty';
    case STAR_FILLED = 'star-filled';
    case STAR_HALF = 'star-half';
    case STICKY = 'sticky';
    case STORE = 'store';
    case SPOTIFY = 'spotify';
    case SUPERHERO = 'superhero';
    case SUPERHERO_ALT = 'superhero-alt';
    case TABLE_COL_AFTER = 'table-col-after';
    case TABLE_COL_BEFORE = 'table-col-before';
    case TABLE_COL_DELETE = 'table-col-delete';
    case TABLE_ROW_AFTER = 'table-row-after';
    case TABLE_ROW_BEFORE = 'table-row-before';
    case TABLE_ROW_DELETE = 'table-row-delete';
    case TABLET = 'tablet';
    case TAG = 'tag';
    case TAGCLOUD = 'tagcloud';
    case TESTIMONIAL = 'testimonial';
    case TEXT = 'text';
    case TEXT_PAGE = 'text-page';
    case THUMBS_DOWN = 'thumbs-down';
    case THUMBS_UP = 'thumbs-up';
    case TICKETS_ALT = 'tickets-alt';
    case TICKETS = 'tickets';
    case TIDE = 'tide';
    case TRANSLATION = 'translation';
    case TRASH = 'trash';
    case TWITCH = 'twitch';
    case TWITTER = 'twitter';
    case TWITTER_ALT = 'twitter-alt';
    case UNDO = 'undo';
    case UNIVERSAL_ACCESS_ALT = 'universal-access-alt';
    case UNIVERSAL_ACCESS = 'universal-access';
    case UNLOCK = 'unlock';
    case UPDATE_ALT = 'update-alt';
    case UPDATE = 'update';
    case UPLOAD = 'upload';
    case VAULT = 'vault';
    case VIDEO_ALT = 'video-alt';
    case VIDEO_ALT_2 = 'video-alt2';
    case VIDEO_ALT_3 = 'video-alt3';
    case VISIBILITY = 'visibility';
    case WARNING = 'warning';
    case WELCOME_ADD_PAGE = 'welcome-add-page';
    case WELCOME_COMMENTS = 'welcome-comments';
    case WELCOME_LEARN_MORE = 'welcome-learn-more';
    case WELCOME_VIEW_SITE = 'welcome-view-site';
    case WELCOME_WIDGETS_MENUS = 'welcome-widgets-menus';
    case WELCOME_WRITE_BLOG = 'welcome-write-blog';
    case WHATSAPP = 'whatsapp';
    case WORDPRESS_ALT = 'wordpress-alt';
    case WORDPRESS = 'wordpress';
    case XING = 'xing';
    case YES_ALT = 'yes-alt';
    case YES = 'yes';
    case YOUTUBE = 'youtube';

    public function withPrefix(): string
    {
        return 'dashicons-' . $this->value;
    }

    public function withoutPrefix(): string
    {
        return $this->value;
    }
}
