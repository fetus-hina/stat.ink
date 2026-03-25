import ScheduleContent from './ScheduleContent';
import classes from './ScheduleContents.module.css';
import esc from 'escape-html';
import { useSelector } from 'react-redux';
import type { IndexRootState } from '../../../store/indexApp';
import type { ScheduleData, ScheduleMode, ScheduleTranslations, TabItem } from '../../../types';

interface ScheduleContentsProps {
  data: TabItem[];
  selected: string;
}

export default function ScheduleContents (props: ScheduleContentsProps) {
  const { data, selected } = props;
  const schedule = useSelector((state: IndexRootState) => state.schedule.data);
  const translations = useSelector((state: IndexRootState) => state.schedule.data ? state.schedule.data.translations : null);

  const mode = (() => {
    const list = data.filter(mode => mode.id === selected);
    return list.length === 1 ? list[0] : null;
  })();

  if (!mode) {
    return (
      <div id='schedule-content' className='tab-content'>
        <div className='tab-pane active' role='tabpanel' />
      </div>
    );
  }

  return (
    <div id='schedule-content' className='tab-content'>
      <div className='tab-pane active' role='tabpanel'>
        {(() => {
          const modeData = extractMode(schedule, mode);
          if (!modeData) {
            return null;
          }

          return (
            <>
              <div className={classes.tabContentRoot}>
                <ScheduleContent
                  mode={modeData.key}
                  modeIcon={modeData.image}
                  schedules={modeData.schedules}
                />
              </div>
              <p
                className='text-right mb-0'
                dangerouslySetInnerHTML={{
                  __html: getDataSourceHTML(schedule, modeData, translations)
                }}
              />
            </>
          );
        })()}
      </div>
    </div>
  );
}

function extractMode (schedule: ScheduleData | null, mode: TabItem): ScheduleMode | null {
  if (schedule === null) {
    return null;
  }

  const ref = mode.ref.slice();
  let current: Record<string, unknown> = Object.assign({}, schedule);
  while (current && ref.length > 0) {
    const curRef = ref.shift()!;
    if (!current[curRef]) {
      return null;
    }
    current = current[curRef] as Record<string, unknown>;
  }
  return current as unknown as ScheduleMode;
}

function getDataSourceHTML (schedule: ScheduleData | null, mode: ScheduleMode, translations: ScheduleTranslations | null) {
  if (
    !schedule ||
    !schedule.sources ||
    !mode ||
    !mode.source ||
    !schedule.sources[mode.source]
  ) {
    return '';
  }

  const source = schedule.sources[mode.source];
  const sourceHTML = `<a href="${esc(source.url)}" target="_blank">${esc(source.name)}</a>`;

  const template = (translations && translations.source)
    ? translations.source
    : 'Source: {source}';

  return esc(template).replace('{source}', sourceHTML);
}
