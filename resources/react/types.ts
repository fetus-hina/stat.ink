// ============================================================
// Counter
// ============================================================
export interface CounterEntry {
  type: 'battle' | 'salmon' | 'user';
  icon: string | null;
  label: string;
  count: number;
}

export type CounterData = Record<string, CounterEntry>;

// ============================================================
// Blog
// ============================================================
export interface BlogEntryTime {
  time: number;
  iso8601: string;
  natural: string;
  relative: string;
}

export interface BlogEntry {
  id: string;
  title: string;
  url: string;
  at: BlogEntryTime;
}

// ============================================================
// Schedule
// ============================================================
export interface ScheduleLocale {
  locale: string;
  timezone: string;
  calendar: string | null;
}

export interface ScheduleSource {
  url: string;
  name: string;
}

export interface ScheduleGameIcon {
  name: string;
  icon: string | null;
}

export interface ScheduleRule {
  key: string;
  name: string;
  short: string;
  icon: string | null;
}

export interface ScheduleMap {
  key: string | null;
  name: string;
  image: string | null;
}

export interface ScheduleWeapon {
  key: string;
  name: string;
  icon: string | null;
}

export interface ScheduleKing {
  key: string;
  name: string;
  image: string | null;
}

export interface ScheduleEntry {
  time: [number, number];
  rule?: ScheduleRule;
  maps: ScheduleMap[];
  weapons?: ScheduleWeapon[];
  king?: ScheduleKing;
  is_big_run?: boolean;
}

export interface ScheduleMode {
  key: string;
  game: string;
  name: string;
  image: string | null;
  source: string;
  schedules: ScheduleEntry[];
}

export interface ScheduleTranslations {
  current_time: string;
  heading: string;
  salmon_open: string;
  source: string;
}

export interface ScheduleData {
  time: number;
  locale: ScheduleLocale;
  sources: Record<string, ScheduleSource>;
  games: Record<string, ScheduleGameIcon>;
  translations: ScheduleTranslations;
  [key: string]: unknown; // splatoon2, splatoon3 etc.
}

// ============================================================
// Battle
// ============================================================
export interface BattleUser {
  icon: string[];
  name: string;
  url: string;
}

export interface BattleMode {
  icon: string | null;
  key: string;
  name: string;
}

export interface BattleRule {
  icon: string | null;
  key: string;
  name: string;
}

export interface BattleStageImages {
  win: string;
  lose: string;
  normal: string;
}

export interface BattleStage {
  image: BattleStageImages;
}

export interface Battle {
  id: string;
  image: string | null;
  thumbnail: string | null;
  isWin: boolean | null;
  mode: BattleMode | null;
  stage: BattleStage | null;
  summary: string | null;
  summary2: string | null;
  time: number;
  rule: BattleRule | null;
  url: string;
  user: BattleUser;
  variant: string;
}

// ============================================================
// Latest Battles / My Latest Battles
// ============================================================
export interface RelTimeTranslations {
  now: string;
  year: { one: string; many: string };
  month: { one: string; many: string };
  day: { one: string; many: string };
  hour: { one: string; many: string };
  minute: { one: string; many: string };
  second: { one: string; many: string };
}

export interface LatestBattlesData {
  battles: Battle[];
  images: {
    noImage: string;
  };
  translations: {
    heading: string;
    reltime: RelTimeTranslations;
  };
  user?: BattleUser | null;
}

// ============================================================
// Tab item (for ScheduleDisplay/ScheduleTabs/ScheduleTab)
// ============================================================
export interface TabItem {
  id: string;
  ref: string[];
  label: string;
  showOpen: boolean;
  priority: number;
}
