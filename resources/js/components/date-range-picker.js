import dayjs from 'dayjs/esm'
import advancedFormat from 'dayjs/plugin/advancedFormat'
import customParseFormat from "dayjs/plugin/customParseFormat";
import localeData from "dayjs/plugin/localeData";
import timezone from 'dayjs/plugin/timezone'
import utc from 'dayjs/plugin/utc'
import isSameOrBefore from 'dayjs/plugin/isSameOrBefore';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';

dayjs.extend(advancedFormat)
dayjs.extend(customParseFormat);
dayjs.extend(localeData);
dayjs.extend(timezone)
dayjs.extend(utc)
dayjs.extend(isSameOrBefore)
dayjs.extend(isSameOrAfter)

export default function dateRangePickerFormComponent({
	state,
	displayFormat = "YYYY-MM-DD",
	minDate = null,
	maxDate = null,
	locale = "en",
	firstDayOfWeek = 0,
	autoClose = false,
	isReadOnly = false,
	isDisabled = false,
	dualCalendar = true,
	enabledDates = null,
}) {
	const timezone = dayjs.tz.guess();

	return {
		state,
		startDisplay: "",
		endDisplay: "",

		start: null,
		end: null,

		hoveredStartDate: null,
		hoveredEndDate: null,

		originalStart: null,
		originalEnd: null,

		currentCalendarMonth1: null,
		currentCalendarYear1: null,
		daysInMonth1: [],
		daysFromPrevMonth1: [], // Days to show from previous month
		daysFromNextMonth1: [],

		currentCalendarMonth2: null,
		currentCalendarYear2: null,
		daysInMonth2: [],
		daysFromPrevMonth2: [],
		daysFromNextMonth2: [],

		activeEnd: "start",
		isAwaitingEndDate: false,

		displayFormat,
		minDate: minDate ? dayjs(minDate) : null,
		maxDate: maxDate ? dayjs(maxDate) : null,
		locale,
		firstDayOfWeek,
		monthNames: [],
		dayNames: [],

		autoClose,
		isReadOnly,
		isDisabled,
		dualCalendar,
		enabledDates,

		init() {
			dayjs.locale(locales[locale] ?? locales['en'])

			this.monthNames = dayjs.months();
			const wdShort = dayjs.weekdaysShort();
			this.dayNames = wdShort.slice(this.firstDayOfWeek).concat(wdShort.slice(0, this.firstDayOfWeek));

			const [start, end] = this.getDatesFromState();
			this.start = start;
			this.end = end;

			this.updateDisplayValues();
			this.setInitialCalendarMonths();
			this.generateCalendars();

			this.$watch("state", (newState) => {
				const [newStart, newEnd] = this.getDatesFromState(newState);

				if (!(this.start && newStart && this.start.isSame(newStart, 'day')) || !this.start === !newStart ||
					!(this.end && newEnd && this.end.isSame(newEnd, 'day')) || !this.end === !newEnd
				) {
					this.start = newStart;
					this.end = newEnd;
					this.updateDisplayValues();
					if (this.isOpen()) this.generateCalendarBasedOnActiveEnd();
				}
			});
		},

		getDatesFromState(currentState = this.state) {
			if (currentState === undefined || currentState === null) {
				return [null, null];
			}

			let start = currentState.start;
			let end = currentState.end;

			if (start) start = dayjs(start);
			if (end) end = dayjs(end);

			return [
				start?.isValid() ? start : null,
				end?.isValid() ? end : null
			];
		},

		updateState() {
			this.state = {
				start: this.start?.format("YYYY-MM-DD"),
				end: this.end?.format("YYYY-MM-DD")
			};
		},

		openCalendar(targetEnd) {
			if (this.isDisabled || this.isReadOnly) return;

			this.activeEnd = targetEnd;
			this.isAwaitingEndDate = (this.activeEnd === 'start' && !this.end) || (this.activeEnd === 'end' && !this.start);
			this.hoveredStartDate = null;
			this.hoveredEndDate = null;

			if (!this.autoClose) {
				this.originalStart = this.start ? this.start.clone() : null;
				this.originalEnd = this.end ? this.end.clone() : null;
			}

			this.setInitialCalendarMonths();
			this.generateCalendars();
			this.$refs.panel.toggle(this.$refs.inputContainer);
		},

		setInitialCalendarMonths() {
			let baseDate = dayjs().tz(timezone);
			if (this.activeEnd === 'start' && this.start) baseDate = this.start;
			else if (this.activeEnd === 'end' && this.end) baseDate = this.end;
			else if (this.start) baseDate = this.start;
			else if (this.end) baseDate = this.end;

			this.currentCalendarMonth1 = baseDate.month();
			this.currentCalendarYear1 = baseDate.year();

			if (this.dualCalendar) {
				const secondCalendarBase = baseDate.add(1, 'month');
				this.currentCalendarMonth2 = secondCalendarBase.month();
				this.currentCalendarYear2 = secondCalendarBase.year();
			}
		},

		generateCalendars() {
			this.generateSingleCalendar(1, this.currentCalendarYear1, this.currentCalendarMonth1);

			if (this.dualCalendar) {
				this.generateSingleCalendar(2, this.currentCalendarYear2, this.currentCalendarMonth2);
			} else {
				this.daysInMonth2 = [];
				this.daysFromPrevMonth2 = [];
				this.daysFromNextMonth2 = [];
			}
		},

		generateSingleCalendar(calendarNum, year, month) {
			if (year === null || month === null) {
				this[`daysInMonth${calendarNum}`] = [];
				this[`daysFromPrevMonth${calendarNum}`] = [];
				this[`daysFromNextMonth${calendarNum}`] = [];
				return;
			}

			const firstDayOfMonth = dayjs(new Date(year, month, 1)).tz(timezone);
			const daysInCurrentMonth = firstDayOfMonth.daysInMonth();

			this[`daysInMonth${calendarNum}`] = Array.from({ length: daysInCurrentMonth }, (_, i) => i + 1);

			// Calculate days from previous month to fill the grid
			const firstDayOfWeekOfMonth = firstDayOfMonth.day(); // 0 (Sun) - 6 (Sat)
			let countFromPrevMonth = (firstDayOfWeekOfMonth - this.firstDayOfWeek + 7) % 7;

			this[`daysFromPrevMonth${calendarNum}`] = [];
			const prevMonth = firstDayOfMonth.subtract(1, 'month');
			const daysInPrevMonth = prevMonth.daysInMonth();
			for (let i = 0; i < countFromPrevMonth; i++) {
				this[`daysFromPrevMonth${calendarNum}`].unshift(daysInPrevMonth - i);
			}

			// Calculate days from next month to fill the grid (total 6 weeks = 42 cells)
			const totalCellsFilled = countFromPrevMonth + daysInCurrentMonth;
			const countFromNextMonth = (42 - totalCellsFilled) % 7 === 0 ? 0 : 42 - totalCellsFilled; // Or fixed 6 weeks: 42 - totalCellsFilled
			// More accurately, fill until 6 rows (42 cells if always 6 rows, or 35 if 5 rows is ok)
			// Let's aim for 6 rows (42 cells) for consistent layout
			// const cellsToFillForSixRows = 42;
			// let countFromNextMonth = cellsToFillForSixRows - totalCellsFilled;
			// if (countFromNextMonth < 0) countFromNextMonth = (7 - (Math.abs(countFromNextMonth) % 7)) %7; // ensure positive or 0

			this[`daysFromNextMonth${calendarNum}`] = [];
			// More robust calculation for next month days to complete 6 rows (42 cells)
			// const remainingCells = 42 - (this[`daysFromPrevMonth${calendarNum}`].length + this[`daysInMonth${calendarNum}`].length);
			// for (let i = 1; i <= remainingCells; i++) {
			//     this[`daysFromNextMonth${calendarNum}`].push(i);
			// }
			// Simpler: just fill up to an even 7 columns if needed
			const lastDayOfMonth = firstDayOfMonth.date(daysInCurrentMonth);
			const lastDayOfWeekOfMonth = lastDayOfMonth.day();
			let nextMonthFillCount = (this.firstDayOfWeek + 6 - lastDayOfWeekOfMonth) % 7;

			this[`daysFromNextMonth${calendarNum}`] = Array.from({ length: nextMonthFillCount }, (_, i) => i + 1);

		},

		applySelectionAndClose() {
			this.originalStart = null;
			this.originalEnd = null;
			this.hoveredStartDate = null;
			this.hoveredEndDate = null;
			this.$refs.panel.toggle(this.$refs.inputContainer);
		},

		cancelSelectionAndClose() {
			this.hoveredStartDate = null;
			this.hoveredEndDate = null;

			if (!this.autoClose) {
				this.revertToOriginalDates();
			}

			this.$refs.panel.toggle(this.$refs.inputContainer);
			this.isAwaitingEndDate = false;
		},

		revertToOriginalDates() {
			if (this.originalStart !== undefined && this.originalEnd !== undefined) {
				this.start = this.originalStart ? this.originalStart.clone() : null;
				this.end = this.originalEnd ? this.originalEnd.clone() : null;
				this.updateDisplayValues();
				this.updateState();
			}
			this.originalStart = null;
			this.originalEnd = null;
		},

		generateCalendarBasedOnActiveEnd() {
			let viewDate = dayjs().tz(timezone);

			if (this.activeEnd === "start" && this.start) viewDate = this.start;
			else if (this.activeEnd === "end" && this.end) viewDate = this.end;
			else if (this.start) viewDate = this.start;
			else if (this.end) viewDate = this.end;

			this.currentCalendarMonth = viewDate.month();
			this.currentCalendarYear = viewDate.year();
			this.generateCalendarDays();
		},

		generateCalendarDays() {
			const firstDayOfMonth = dayjs(new Date(this.currentCalendarYear, this.currentCalendarMonth, 1)).tz(timezone);
			const daysInMonthVal = firstDayOfMonth.daysInMonth();
			const dayOffset = (firstDayOfMonth.day() - this.firstDayOfWeek + 7) % 7;

			this.blankDays = Array.from({ length: dayOffset }, (_, i) => i + 1);
			this.daysInMonth = Array.from({ length: daysInMonthVal }, (_, i) => i + 1);
		},

		previousMonth() {
			if (this.isPreviousMonthDisabled()) return; // This will need to check based on month1

			const cal1Date = dayjs(new Date(this.currentCalendarYear1, this.currentCalendarMonth1, 1)).tz(timezone);
			const newCal1Date = cal1Date.subtract(1, 'month');
			this.currentCalendarMonth1 = newCal1Date.month();
			this.currentCalendarYear1 = newCal1Date.year();

			if (this.dualCalendar) {
				const newCal2Date = newCal1Date.add(1, 'month');
				this.currentCalendarMonth2 = newCal2Date.month();
				this.currentCalendarYear2 = newCal2Date.year();
			}

			this.generateCalendars();
		},

		nextMonth() {
			if (this.isNextMonthDisabled()) return;

			const newCal1Date = this.dualCalendar ?
				dayjs(new Date(this.currentCalendarYear2, this.currentCalendarMonth2, 1)).tz(timezone) :
				dayjs(new Date(this.currentCalendarYear1, this.currentCalendarMonth1, 1)).tz(timezone).add(1, 'month');

			this.currentCalendarMonth1 = newCal1Date.month();
			this.currentCalendarYear1 = newCal1Date.year();

			if (this.dualCalendar) {
				const newCal2Date = newCal1Date.add(1, 'month');
				this.currentCalendarMonth2 = newCal2Date.month();
				this.currentCalendarYear2 = newCal2Date.year();
			}

			this.generateCalendars();
		},

		isPreviousMonthDisabled() {
			if (!this.minDate) return false;
			const prevMonthOfCal1 = dayjs(new Date(this.currentCalendarYear1, this.currentCalendarMonth1, 1)).tz(timezone).subtract(1, 'month');
			return prevMonthOfCal1.endOf('month').isBefore(this.minDate.startOf('month'));
		},

		isNextMonthDisabled() {
			if (!this.maxDate) return false;
			const monthToCompare = this.dualCalendar ? this.currentCalendarMonth2 : this.currentCalendarMonth1;
			const yearToCompare = this.dualCalendar ? this.currentCalendarYear2 : this.currentCalendarYear1;

			const nextMonthToDisplay = dayjs(new Date(yearToCompare, monthToCompare, 1)).tz(timezone).add(1, 'month');
			return nextMonthToDisplay.startOf('month').isAfter(this.maxDate.endOf('month'));
		},

		selectDay(day, month, year) {
			const selectedDate = dayjs(new Date(year, month, day)).tz(timezone);
			if (this.isDayDisabledInternal(selectedDate)) return;

			this.hoveredStartDate = null;
			this.hoveredEndDate = null;

			let rangeCompleted = false;
			let shouldSwitchActiveEnd = false;

			if (this.activeEnd === 'start') {
				this.start = selectedDate;
				if (this.end && this.start.isAfter(this.end, 'day')) {
					this.end = null;
					this.isAwaitingEndDate = true;
					this.activeEnd = 'end';
					shouldSwitchActiveEnd = false;
				} else if (!this.end) {
					this.isAwaitingEndDate = true;
					this.activeEnd = 'end';
					shouldSwitchActiveEnd = false;
				} else {
					// Check if the range is continuous before completing
					if (this.isRangeContinuous(this.start, this.end)) {
						this.isAwaitingEndDate = false;
						rangeCompleted = true;
					} else {
						// Range is not continuous, reset end date
						this.end = null;
						this.isAwaitingEndDate = true;
						this.activeEnd = 'end';
						shouldSwitchActiveEnd = false;
					}
				}
			} else { // activeEnd === 'end'
				const potentialEnd = selectedDate;
				if (this.start && potentialEnd.isBefore(this.start, 'day')) {
					this.start = potentialEnd.clone();
					this.end = null;
					this.isAwaitingEndDate = true;
					this.activeEnd = 'end';
					shouldSwitchActiveEnd = false;
				} else if (!this.start) {
					this.start = potentialEnd.clone();
					this.isAwaitingEndDate = false;
					rangeCompleted = true;
				} else {
					// Check if the range is continuous before setting the end date
					if (this.isRangeContinuous(this.start, potentialEnd)) {
						this.end = potentialEnd;
						this.isAwaitingEndDate = false;
						rangeCompleted = true;
					} else {
						// Range is not continuous, don't set the end date
						return;
					}
				}
			}

			this.updateDisplayValues();
			this.updateState();

			if (rangeCompleted && this.autoClose) {
				this.applySelectionAndClose();
			} else if (shouldSwitchActiveEnd) {
				this.activeEnd = 'end';
			}
		},

		previewDay(day, month, year) {
			const hoverDate = dayjs(new Date(year, month, day)).tz(timezone);
			if (this.isDayDisabledInternal(hoverDate)) {
				this.hoveredStartDate = null;
				this.hoveredEndDate = null;
				return;
			}

			if (this.activeEnd === 'start') {
				this.hoveredStartDate = hoverDate;
				this.hoveredEndDate = null;
			} else if (this.activeEnd === 'end' && this.start) {
				if (hoverDate.isBefore(this.start, 'day')) {
					this.hoveredEndDate = null;
				} else {
					// Only show hover if the range would be continuous
					if (this.isRangeContinuous(this.start, hoverDate)) {
						this.hoveredEndDate = hoverDate;
					} else {
						this.hoveredEndDate = null;
					}
				}
				this.hoveredStartDate = null; // Clear other preview
			} else {
				this.hoveredStartDate = null;
				this.hoveredEndDate = null;
			}
		},

		clearPreview() {
			this.hoveredStartDate = null;
			this.hoveredEndDate = null;
		},

		updateDisplayValues() {
			this.startDisplay = this.start
				? this.start.format(this.displayFormat)
				: "";
			this.endDisplay = this.end
				? this.end.format(this.displayFormat)
				: "";
		},

		clearDateTarget(target) {
			if (target === 'start') {
				this.start = null;
			} else if (target === 'end') {
				this.end = null;
			}
			this.updateDisplayValues();
			this.updateState();
			this.isAwaitingEndDate = (this.start && !this.end);
			if (!this.start && this.activeEnd === 'end') this.activeEnd = 'start';
			else if (this.start && !this.end) this.activeEnd = 'end';
			if (this.isOpen()) this.generateCalendarBasedOnActiveEnd();
		},

		isDayDisabledInternal(dateAsDayjs) {
			if (this.minDate && dateAsDayjs.isBefore(this.minDate, "day")) return true;
			if (this.maxDate && dateAsDayjs.isAfter(this.maxDate, "day")) return true;
			
			// If enabledDates is provided, only those dates are allowed
			if (this.enabledDates && Array.isArray(this.enabledDates)) {
				const dateString = dateAsDayjs.format('YYYY-MM-DD');
				return !this.enabledDates.includes(dateString);
			}
			
			return false;
		},

		isRangeContinuous(startDate, endDate) {
			if (!this.enabledDates || !Array.isArray(this.enabledDates)) return true;
			if (!startDate || !endDate) return true;
			
			const start = startDate.isBefore(endDate) ? startDate : endDate;
			const end = startDate.isBefore(endDate) ? endDate : startDate;
			
			let current = start.clone();
			while (current.isSameOrBefore(end, 'day')) {
				const dateString = current.format('YYYY-MM-DD');
				if (!this.enabledDates.includes(dateString)) {
					return false;
				}
				current = current.add(1, 'day');
			}
			
			return true;
		},

		isDayDisabled(day, month, year) {
			return this.isDayDisabledInternal(dayjs(new Date(year, month, day)).tz(timezone));
		},

		isToday(day, month, year) {
			return dayjs(new Date(year, month, day))
				.tz(timezone)
				.isSame(dayjs().tz(timezone), "day");
		},

		isStartDay(day, month, year) {
			const dateToCompare = this.activeEnd === 'start' && this.hoveredStartDate ? this.hoveredStartDate : this.start;
			if (!dateToCompare) return false;
			return dayjs(new Date(year, month, day)).tz(timezone).isSame(dateToCompare, "day");
		},

		isEndDay(day, month, year) {
			const dateToCompare = this.activeEnd === 'end' && this.hoveredEndDate ? this.hoveredEndDate : this.end;
			if (!dateToCompare) return false;
			return dayjs(new Date(year, month, day)).tz(timezone).isSame(dateToCompare, "day");
		},

		isDaySelected(day, month, year) {
			return this.isStartDay(day, month, year) || this.isEndDay(day, month, year);
		},

		isInRange(day, month, year) {
			const currentActiveStart = this.activeEnd === 'start' && this.hoveredStartDate ? this.hoveredStartDate : this.start;
			const currentActiveEnd = this.activeEnd === 'end' && this.hoveredEndDate ? this.hoveredEndDate : this.end;

			const s = currentActiveStart || this.start;
			const e = currentActiveEnd || this.end;

			if (!s || !e || s.isSame(e, "day")) return false;

			const d = dayjs(new Date(year, month, day)).tz(timezone);

			const startRange = s.isBefore(e) ? s : e;
			const endRange = s.isBefore(e) ? e : s;

			return d.isAfter(startRange, "day") && d.isBefore(endRange, "day");
		},

		isOpen() {
			return this.$refs.panel?.style.display === 'block';
		},
	};
}

const locales = {
	ar: require('dayjs/locale/ar'),
	bs: require('dayjs/locale/bs'),
	ca: require('dayjs/locale/ca'),
	ckb: require('dayjs/locale/ku'),
	cs: require('dayjs/locale/cs'),
	cy: require('dayjs/locale/cy'),
	da: require('dayjs/locale/da'),
	de: require('dayjs/locale/de'),
	en: require('dayjs/locale/en'),
	es: require('dayjs/locale/es'),
	et: require('dayjs/locale/et'),
	fa: require('dayjs/locale/fa'),
	fi: require('dayjs/locale/fi'),
	fr: require('dayjs/locale/fr'),
	hi: require('dayjs/locale/hi'),
	hu: require('dayjs/locale/hu'),
	hy: require('dayjs/locale/hy-am'),
	id: require('dayjs/locale/id'),
	it: require('dayjs/locale/it'),
	ja: require('dayjs/locale/ja'),
	ka: require('dayjs/locale/ka'),
	km: require('dayjs/locale/km'),
	ko: require('dayjs/locale/ko'),
	ku: require('dayjs/locale/ku'),
	lt: require('dayjs/locale/lt'),
	lv: require('dayjs/locale/lv'),
	ms: require('dayjs/locale/ms'),
	my: require('dayjs/locale/my'),
	nl: require('dayjs/locale/nl'),
	no: require('dayjs/locale/nb'),
	pl: require('dayjs/locale/pl'),
	pt_BR: require('dayjs/locale/pt-br'),
	pt_PT: require('dayjs/locale/pt'),
	ro: require('dayjs/locale/ro'),
	ru: require('dayjs/locale/ru'),
	sv: require('dayjs/locale/sv'),
	th: require('dayjs/locale/th'),
	tr: require('dayjs/locale/tr'),
	uk: require('dayjs/locale/uk'),
	vi: require('dayjs/locale/vi'),
	zh_CN: require('dayjs/locale/zh-cn'),
	zh_TW: require('dayjs/locale/zh-tw'),
}
